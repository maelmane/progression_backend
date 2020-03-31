<?php

require_once('controleur.php');

class ControleurSérie extends Controleur{

	function get_page_infos(){
		$this->série=$this->get_série();
		$this->questions=$this->série?$this->get_questions():null;

		$thème=new Theme($this->série->themeID);
		return array("template"=>"serie",
					 "serie"=>$this->série,
					 "titre"=>$thème->titre,
					 "questions"=>$this->questions);

	}

	function get_série(){
		$serie=new Serie($this->id);
		
		return $serie;
	}

	function get_questions(){
		$questions=$this->série->get_questions();
		$this->calculer_réussite($questions);

		return $questions;
	}

	function calculer_réussite($questions){
		foreach($questions as $question){
			$question->réussie=$question->get_avancement($this->user_id)->get_etat() == Question::ETAT_REUSSI;
		}
	}

}
