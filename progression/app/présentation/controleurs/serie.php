<?php

require_once __DIR__.'/controleur.php';
require_once 'domaine/interacteurs/obtenir_theme.php';
require_once 'domaine/interacteurs/obtenir_serie.php';
require_once 'domaine/interacteurs/obtenir_question.php';

class SérieCtl extends Controleur{


	function __construct( $source, $série_id, $user_id ){
		parent::__construct( $source, $user_id );
		
		$this->_série=( new ObtenirSérieInt( $this->_source, $user_id ))->get_série( $série_id );
		$this->_questions=( new ObtenirQuestionInt( $this->_source, $user_id ))->get_questions_par_série( $série_id );
		
		$thème_id=$this->_série->thème_id;
		$this->_thème = ( new ObtenirThèmeInt( $this->_source, $user_id ))->get_thème( $thème_id );
	}
	
	function get_page_infos(){
		return array( parent::get_page_infos(),
					 "template"=>"serie",
					 "serie"=>$this->_série,
					 "titre"=>$this->_thème->titre,
					 "questions"=>$this->_questions );

	}

}
