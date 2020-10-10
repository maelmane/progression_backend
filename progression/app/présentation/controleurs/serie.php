<?php

require_once('controleur.php');

class ControleurSérie extends Controleur{


	function __construct($source, $série_id, $user_id){
		parent::__construct($source, $user_id);
		
		$interacteur = new SérieInteracteur($this->_source, $user_id);
		$this->_série=$interacteur->get_série($série_id);
		$this->_questions=$interacteur->get_questions($série_id);
		
		$thème_id=$this->_série->thème_id;
		$this->_thème = (new ThèmeInteracteur($this->_source, $user_id))->get_thème($thème_id);
	}
	
	function get_page_infos(){
		return array(parent::get_page_infos(),
					 "template"=>"serie",
					 "serie"=>$this->_série,
					 "titre"=>$this->_thème->titre,
					 "questions"=>$this->_questions);

	}

}
