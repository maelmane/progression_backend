<?php

require_once('controleur.php');
require_once('domaine/interacteurs/theme_interacteur.php');
require_once('domaine/interacteurs/user_interacteur.php');

class ControleurAccueil extends Controleur{

	function __construct($source, $user_id){
		parent::__construct($source, $user_id);
		$this->_user_id = $user_id;

		$interacteur = new ThèmeInteracteur($this->_source, $this->_user_id);
		$this->_thèmes=$interacteur->get_thèmes();
		$this->calculer_avancement();

	}
	
	function get_page_infos(){
		return array(
			parent::get_page_infos(),
			"themes"=>$this->_thèmes,
			"titre"=>"Taleau de bord",
			"template"=>"accueil"
		);
	}

	function calculer_avancement(){
		foreach($this->_thèmes as $thème){
			$interacteur = new UserInteracteur($this->_source);
			$thème->avancement=$interacteur->get_pourcentage_avancement($this->_user_id, $thème->id);
		}
	}
	
}

?>
