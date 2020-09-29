<?php

require_once('présentation/controleurs/controleur.php');
require_once('domaine/entités/theme.php');
require_once('domaine/interacteurs/theme_interacteur.php');

class ControleurThème extends Controleur{

	function __construct($source, $thème_id, $user_id){
		parent::__construct($source);
		$this->_user_id = $user_id;
		$this->_thème_id = $thème_id;
	}
	
	function get_page_infos(){
		$interacteur = new ThèmeInteracteur($this->_source, $this->_user_id);
		$this->thème = $interacteur->get_thème($this->_thème_id);
		$this->séries = $interacteur->get_séries($this->_thème_id);
		
		return array("template"=>"theme",
					 "titre"=>$this->thème->titre,
					 'theme'=>$this->thème,
					 'series'=>$this->séries);
		
	}

}

?>
