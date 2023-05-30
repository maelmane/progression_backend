<?php
/*
   This file is part of Progression.

   Progression is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Progression is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Progression.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace progression\domaine\interacteur;

use LDAP\Result;
use progression\domaine\entité\clé\{Clé, Portée};
use progression\domaine\entité\user\Rôle;
use progression\dao\{UserDAO, DAOException};

class LoginInt extends Interacteur
{
	function effectuer_login_par_clé($username, $nom_clé, $secret)
	{
		$dao = $this->source_dao->get_clé_dao();

		$clé = $dao->get_clé($username, $nom_clé);
		if (
			$clé &&
			$clé->est_valide() &&
			$clé->portée == Portée::AUTH &&
			$dao->vérifier($username, $nom_clé, $secret)
		) {
			$dao = $this->source_dao->get_user_dao();
			return $dao->get_user($username);
		} else {
			return null;
		}
	}

	function effectuer_login_par_identifiant($username, $password = null, $domaine = null)
	{
		if (!$this->vérifier_champ_valide($username)) {
			return null;
		}

		$user = null;
		$auth_local = getenv("AUTH_LOCAL") === "true";
		$auth_ldap = getenv("AUTH_LDAP") === "true";

		try {
			if ($auth_ldap && $domaine) {
				// LDAP
				$user = $this->login_ldap($username, $password, $domaine);
			} elseif ($auth_local) {
				// Local
				$user = $this->login_local($username, $password);
			} elseif (!$auth_ldap) {
				// Sans authentification
				$user = $this->login_sans_authentification($username);
			}
		} catch (DAOException $e) {
			throw new IntéracteurException($e, 503);
		}

		return $user;
	}

	function login_local(string $identifiant, string $password)
	{
		$obtenirUserInt = new ObtenirUserInt();
		$user =
			strpos($identifiant, "@") === false
				? $obtenirUserInt->get_user($identifiant)
				: $obtenirUserInt->trouver(courriel: $identifiant);
		if ($user && $this->source_dao->get_user_dao()->vérifier_password($user, $password)) {
			return $user;
		} else {
			return null;
		}
	}

	function login_ldap(string $identifiant, string $password, string $domaine)
	{
		$user = null;
		if ($this->get_username_ldap($identifiant, $password, $domaine)) {
			$user = $this->login_sans_authentification($identifiant);
		}

		return $user;
	}

	function vérifier_champ_valide($champ)
	{
		return $champ && !empty(trim($champ));
	}

	function get_username_ldap(string $identifiant, string $password, string $domaine)
	{
		if ($domaine != getenv("LDAP_DOMAINE")) {
			throw new IntéracteurException("Domaine multiple non implémenté", 500);
		}

		// Connexion au serveur LDAP
		$ldap = @ldap_connect("ldap://" . getenv("LDAP_HOTE"), (int) getenv("LDAP_PORT"));
		if (!$ldap) {
			syslog(LOG_ERR, "Erreur de configuration LDAP");
			throw new IntéracteurException("Erreur de configuration LDAP", 500);
		}
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, (int) getenv("LDAP_TIMEOUT"));

		// Bind l'utilisateur LDAP
		if (getenv("LDAP_DN_BIND") && getenv("LDAP_PW_BIND")) {
			$bind = @ldap_bind($ldap, getenv("LDAP_DN_BIND"), getenv("LDAP_PW_BIND"));
		} else {
			$bind = @ldap_bind($ldap, $identifiant, $password);
		}

		if (!$bind) {
			ldap_get_option($ldap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
			syslog(LOG_ERR, "Erreur de connexion à LDAP : $extended_error");
			throw new IntéracteurException("Impossible de se connecter au serveur d'authentification", 503);
		}

		//Recherche de l'utilisateur à authentifier
		$result = @ldap_search($ldap, getenv("LDAP_BASE") ?: "", "({getenv('LDAP_UID')}=$identifiant)", [
			"dn",
			"cn",
			1,
		]);
		if ($result instanceof Result) {
			$user = ldap_get_entries($ldap, $result);
			return $user &&
				isset($user["count"]) &&
				$user["count"] == 1 &&
				isset($user[0]) &&
				is_array($user[0]) &&
				isset($user[0]["dn"]) &&
				@ldap_bind($ldap, $user[0]["dn"], $password);
		}
		return null;
	}

	function login_sans_authentification($username)
	{
		return (new InscriptionInt())->effectuer_inscription_sans_mdp($username, Rôle::NORMAL);
	}
}
