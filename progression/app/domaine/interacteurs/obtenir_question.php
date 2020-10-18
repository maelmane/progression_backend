<?php

require_once 'domaine/entités/serie.php';
require_once __DIR__.'/interacteur.php';

class ObtenirQuestionInt extends Interacteur {

	function __construct( $source, $user_id ) {
		parent::__construct( $source );
		$this->_user_id=$user_id;
	}
	
	function get_question( $question_id ){
		return $this->_source->get_question_dao()->get_question( $question_id );
	}	

	function get_questions_par_série( $série_id ){
		$série = new Série( $série_id );
		$questions = $this->_source->get_question_dao()->get_questions_par_série( $série_id );

		$this->calculer_réussite( $questions );

		return $questions;
	}

	private function calculer_réussite( $questions ){
		$interacteur = new ObtenirAvancementInt( $this->_source, $this->_user_id );
		
		foreach( $questions as $question ){
			$question->réussie=$interacteur->get_avancement( $question->id )->etat == Question::ETAT_REUSSI;
		}
	}

}
