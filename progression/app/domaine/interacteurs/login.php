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
?><?php

require_once 'config.php';
require_once __DIR__ . '/obtenir_user.php';
require_once __DIR__ . '/creer_user.php';

class LoginInt extends Interacteur
{
    function __construct($source)
    {
        parent::__construct($source);
    }

    function effectuer_login($username, $password)
    {
        syslog(LOG_INFO, "Tentative de connexion : " . $username);

        if ($GLOBALS['config']['auth_type'] == "no") {
            $user = $this->login_sans_authentification($username);
        } elseif ($GLOBALS['config']['auth_type'] == "local") {
            $user = $this->login_local($username, $password);
        } elseif ($GLOBALS['config']['auth_type'] == "ldap") {
            $user = $this->login_ldap($username, $password);
        }

        if ($user != null) {
            syslog(LOG_INFO, "Connexion réussie: " . $username);
        }

        return $user;
    }

    function login_local($username, $password)
    {
        throw new ConnexionException(
            "L'authentification locale n'est pas implémentée."
        );
    }

    function login_ldap($username, $password)
    {
        $user = null;

        if ($this->vérifier_champs_valides($username, $password)) {
            $user_ldap = LoginInt::get_username_ldap($username, $password);

            if ($user_ldap != null) {
                $user = (new CréerUserInt(
                    $this->_source
                ))->obtenir_ou_créer_user($username);
            }
        }

        return $user;
    }

    function vérifier_champs_valides($username, $password)
    {
        return !empty(trim($username) || empty($password));
    }

    function get_username_ldap($username, $password)
    {
        #Tentative de connexion à AD
        define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

        ($ldap = ldap_connect(
            "ldap://" . $GLOBALS['config']['hote_ad'],
            $GLOBALS['config']['port_ad']
        )) or die("Configuration de serveur LDAP invalide.");
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        $bind = @ldap_bind(
            $ldap,
            $GLOBALS['config']['dn_bind'],
            $GLOBALS['config']['pw_bind']
        );

        if (!$bind) {
            ldap_get_option(
                $ldap,
                LDAP_OPT_DIAGNOSTIC_MESSAGE,
                $extended_error
            );
            throw new ConnexionException(
                "Impossible de se connecter au serveur d'authentification. Veuillez communiquer avec l'administrateur du site. Erreur : $extended_error"
            );
        }
        $result = ldap_search(
            $ldap,
            $GLOBALS['config']['ldap_base'],
            "(sAMAccountName=$username)",
            ['dn', 'cn', 1]
        );
        $user = ldap_get_entries($ldap, $result);
        if (
            $user["count"] != 1 ||
            !@ldap_bind($ldap, $user[0]['dn'], $password)
        ) {
            return null;
        }
        return $user[0];
    }

    function login_sans_authentification($username)
    {
        return (new CréerUserInt($this->_source))->obtenir_ou_créer_user(
            $username
        );
    }
}

?>
