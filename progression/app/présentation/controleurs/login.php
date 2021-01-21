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

class ConnexionException extends Exception
{
}

require_once 'config.php';
require_once __DIR__ . '/controleur.php';
require_once 'domaine/interacteurs/login.php';

class LoginCtl extends Controleur
{
    function __construct($source)
    {
        parent::__construct($source, null);
    }

    private function set_infos_session($user)
    {
        #Obtient les infos de l'utilisateur
        $_SESSION["user_id"] = $user->id;
    }

    private function récupérer_configs()
    {
        $configs = [
            "domaine_mail" => $GLOBALS['config']['domaine_mail'],
            "password" => $GLOBALS['config']['auth_type'] != "no" ? "true" : "",
        ];
        return $configs;
    }

    function effectuer_login($username, $password)
    {
        $user = (new LoginInt($this->_source))->effectuer_login(
            $username,
            $password
        );

        if ($user != null) {
            $this->set_infos_session($user);
        }

        return $user;
    }

    function get_page_infos()
    {
        if (isset($_REQUEST["submit"])) {
            $user = $this->effectuer_login(
                $_REQUEST["username"],
                isset($_REQUEST["passwd"]) ? $_REQUEST["passwd"] : null
            );

            if ($user != null) {
                return (new AccueilCtl(
                    new DAOFactory(),
                    $user->id
                ))->get_page_infos();
            } else {
                $this->_erreurs[] =
                    "Nom d'utilisateur ou mot de passe invalide.";
            }
        }

        return array_merge(
            parent::get_page_infos(),
            [
                "template" => "login",
                "titre" => "Connexion",
            ],
            $this->récupérer_configs()
        );
    }
}
