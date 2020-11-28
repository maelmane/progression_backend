<?php

require_once __DIR__ . '/controleur.php';
require_once 'domaine/interacteurs/obtenir_user.php';
require_once 'domaine/interacteurs/mettre_à_jour_thèmes.php';

class AdminCtl extends Controleur
{
    function __construct($source, $user_id)
    {
        parent::__construct($source, $user_id);
    }

    function get_page_infos()
    {
        $user = (new ObtenirUserInt($this->_source))->get_user($this->_user_id);
        if ($user->role == User::ROLE_ADMIN) {
            if (isset($_REQUEST["submit"])) {
                $url = $_REQUEST["url"];
                $username = $_REQUEST["username"];
                $password = $_REQUEST["password"];
                if (
                    !(new MettreÀJourThèmesInt())->exécuter(
                        $url,
                        $username,
                        $password
                    )
                ) {
                    $this->_erreurs += ["Erreur d'importation"];
                }
            }

            return array_merge(parent::get_page_infos(), [
                "titre" => "Page d'administration",
                "template" => "admin",
            ]);
        } else {
            return (new AccueilCtl(
                $this->_source,
                $this->_user_id
            ))->get_page_infos();
        }
    }
}

?>
