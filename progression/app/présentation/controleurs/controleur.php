<?php

require_once 'domaine/interacteurs/obtenir_user.php';

class Controleur
{
    function __construct($source, $user_id)
    {
        $this->_source = $source;
        $this->_user_id = $user_id;
        $this->_erreurs = [];
    }

    function get_page_infos()
    {
        return [
            "erreurs" => $this->_erreurs,
            "user" => (new ObtenirUserInt($this->_source))->get_user(
                $this->_user_id
            ),
        ];
    }
}
?>
