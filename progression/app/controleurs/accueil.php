<?php

require_once('controleur.php');

class ControleurAccueil extends Controleur{

	function get_page_infos(){
		$this->thèmes=get_themes();
		$this->calculer_avancement();

		return array(
			"themes"=>$this->thèmes,
			"titre"=>"Taleau de bord",
			"template"=>"accueil"
		);
	}

	function calculer_avancement(){
		foreach($this->thèmes as $thème){
			$thème->avancement=$thème->get_pourcentage_avancement($this->user_id);
		}
	}
	
}

?>
