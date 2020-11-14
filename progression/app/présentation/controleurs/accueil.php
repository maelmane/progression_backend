<?php

require_once __DIR__ . '/controleur.php';
require_once 'domaine/interacteurs/obtenir_theme.php';

class AccueilCtl extends Controleur
{
    function __construct($source, $user_id)
    {
        parent::__construct($source, $user_id);

        $interacteur = new ObtenirThèmeInt($this->_source, $this->_user_id);
        $this->_thèmes = $interacteur->get_thèmes();
    }

    function get_page_infos()
    {
        return array_merge(parent::get_page_infos(), [
            "themes" => $this->_thèmes,
            "titre" => "Taleau de bord",
            "template" => "accueil",
        ]);
    }
}

?>
