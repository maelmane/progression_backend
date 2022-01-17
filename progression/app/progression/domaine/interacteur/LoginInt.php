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

use progression\domaine\entité\Clé;
use progression\dao\{DAOFactory, UserDAO};

class AuthException extends \Exception
{
}

class LoginInt extends Interacteur
{
	function effectuer_login_par_clé($username, $nom_clé, $secret)
	{
		$dao = DAOFactory::getInstance()->get_clé_dao();

		$clé = $dao->get_clé($username, $nom_clé);
		if (
			$clé &&
			$clé->est_valide() &&
			$clé->portée == Clé::PORTEE_AUTH &&
			$dao->vérifier($username, $nom_clé, $secret)
		) {
			$dao = DAOFactory::getInstance()->get_user_dao();
			return $dao->get_user($username);
		} else {
			syslog(LOG_NOTICE, "Clé invalide pour $username");
			return null;
		}
	}

	function effectuer_login_par_identifiant($username, $password = null, $domaine = null)
	{
		syslog(LOG_INFO, "Tentative de connexion : $username");

		if (!$this->vérifier_champ_valide($username)) {
			return null;
		}

		$user = null;

		$auth_local = getenv("AUTH_LOCAL") !== "false";
		$auth_ldap = getenv("AUTH_LDAP") === "true";

		if ($auth_ldap && $domaine) {
			$user = $this->login_ldap($username, $password, $domaine);
		} elseif ($auth_local) {
			$user = $this->login_local($username, $password);
		} elseif (!$auth_ldap && !$auth_local) {
			$user = $this->login_sans_authentification($username);
		}

		if ($user != null) {
			syslog(LOG_INFO, "Connexion réussie : $username");
		}

		return $user;
	}

	function login_local($username, $password)
	{
		$user = (new ObtenirUserInt())->get_user($username);

		if ($user && $this->source_dao->get_user_dao()->vérifier_password($user, $password)) {
			return $user;
		} else {
			return null;
		}
	}

	function login_ldap($username, $password, $domaine)
	{
		$user = null;
		if ($this->get_username_ldap($username, $password, $domaine)) {
			$user = $this->login_sans_authentification($username);
		}

		return $user;
	}

	function vérifier_champ_valide($champ)
	{
		return $champ && !empty(trim($champ));
	}

	function get_username_ldap($username, $password, $domaine)
	{
		if ($domaine != $_ENV["LDAP_DOMAINE"]) {
			throw new \Exception("Domaine multiple non implémenté");
		}

		// Connexion au serveur LDAP
		$ldap = ldap_connect("ldap://" . $_ENV["LDAP_HOTE"], $_ENV["LDAP_PORT"]);
		if (!$ldap) {
			syslog(LOG_ERR, "Erreur de configuration LDAP");
			throw new \Exception("Erreur de configuration LDAP");
		}
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		// Bind l'utilisateur LDAP
		if ($_ENV["LDAP_DN_BIND"] && $_ENV["LDAP_PW_BIND"]) {
			$bind = ldap_bind($ldap, $_ENV["LDAP_DN_BIND"], $_ENV["LDAP_PW_BIND"]);
		} else {
			$bind = ldap_bind($ldap, $username, $password);
		}

		if (!$bind) {
			ldap_get_option($ldap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
			syslog(LOG_ERR, "Erreur de connexion à LDAP : $extended_error");
			throw new AuthException(
				"Impossible de se connecter au serveur d'authentification. Veuillez communiquer avec l'administrateur du site. Erreur : $extended_error",
			);
		}

		//Recherche de l'utilisateur à authentifier
		$result = ldap_search($ldap, $_ENV["LDAP_BASE"], "({$_ENV["LDAP_UID"]}=$username)", ["dn", "cn", 1]);
		$user = ldap_get_entries($ldap, $result);
		if ($user["count"] != 1 || !@ldap_bind($ldap, $user[0]["dn"], $password)) {
			return null;
		} else {
			return true;
		}
	}

	function login_sans_authentification($username)
	{
		$user = (new ObtenirUserInt())->get_user($username);
		if (!$user) {
			$user = (new CréerUserInt())->créer_user($username);
		}

		return $user;
	}
}
