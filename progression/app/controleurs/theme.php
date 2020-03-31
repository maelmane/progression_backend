<?php

require_once('controleur.php');

class ControleurThème extends Controleur{

	function get_page_infos(){
		$this->thème=$this->get_theme();
		$this->séries=$this->thème?$this->get_series():null;
		return array("template"=>"theme",
					 "titre"=>$this->thème->titre,
					 'theme'=>$this->thème,
					 'series'=>$this->séries);
		
	}

	function get_theme(){
		$theme=new Theme($this->id, $this->user_id);

		return $theme->id ? $theme : null;
	}

	function get_series(){
		$séries=$this->thème->get_series();
		$this->calculer_avancement($séries);

		return $séries;
	}

	function calculer_avancement($series){
		foreach($series as $serie){
			$serie->avancement=$serie->get_pourcentage_avancement($this->user_id);
		}
	}
}

?>
