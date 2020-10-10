<?php

require_once __DIR__.'/controleur.php';
require_once 'domaine/interacteurs/obtenir_theme.php';

class AccueilCtl extends Controleur{

	function __construct($source, $user_id){
		parent::__construct($source, $user_id);

		$interacteur = new ObtenirThèmeInt($this->_source, $this->_user_id);
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

	private function calculer_avancement(){
		foreach($this->_thèmes as $thème){
			$interacteur = new ObtenirThèmeInt($this->_source, $this->_user_id);
			$thème->avancement=$interacteur->get_pourcentage_avancement($thème->id);
		}
	}
	
}

?>
