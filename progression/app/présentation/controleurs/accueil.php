<?php

require_once('controleur.php');
require_once('domaine/interacteurs/theme_interacteur.php');
require_once('domaine/interacteurs/user_interacteur.php');

class ControleurAccueil extends Controleur{

	function __construct($source, $user_id){
		parent::__construct($source);
		$this->_user_id = $user_id;
	}
	
	function get_page_infos(){
		$interacteur = new ThemeInteracteur($this->_source);
		$this->thèmes=$interacteur->get_themes($this->_source->get_user_dao()->get_user($this->_user_id));
		$this->calculer_avancement();

		return array(
			"themes"=>$this->thèmes,
			"titre"=>"Taleau de bord",
			"template"=>"accueil"
		);
	}

	function calculer_avancement(){
		foreach($this->thèmes as $thème){
			$interacteur = new UserInteracteur($this->_source);
			$thème->avancement=$interacteur->get_pourcentage_avancement($this->_user_id, $thème->id);
		}
	}
	
}

?>
