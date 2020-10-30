<?php

require_once 'domaine/entités/question_prog.php';
require_once __DIR__.'/interacteur.php';

class ObtenirQuestionProgEvalInt extends ObtenirQuestionInt {

	public function get_question( $question_id ){
		return $this->_source->get_question_prog_eval_dao()->get_question( $question_id );
	}	

}
