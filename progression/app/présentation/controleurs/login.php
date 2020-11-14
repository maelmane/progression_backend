<?php

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
