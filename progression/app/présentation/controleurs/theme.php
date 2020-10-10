<?php

require_once('présentation/controleurs/controleur.php');
require_once('domaine/entités/theme.php');
require_once('domaine/interacteurs/theme_interacteur.php');

class ControleurThème extends Controleur{

	function __construct($source, $thème_id, $user_id){
		parent::__construct($source, $user_id);

		$interacteur = new ThèmeInteracteur($this->_source, $user_id);
		$this->_thème = $interacteur->get_thème($thème_id);
		$this->_séries = $interacteur->get_séries($thème_id);
		
	}
	
	function get_page_infos(){
		return array( parent::get_page_infos(),
					  "template"=>"theme",
					 "titre"=>$this->_thème->titre,
					 'theme'=>$this->_thème,
					 'series'=>$this->_séries);
		
	}

}

?>
