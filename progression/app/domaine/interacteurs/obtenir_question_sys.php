<?php

require_once 'domaine/entités/question_sys.php';
require_once __DIR__.'/interacteur.php';

class ObtenirQuestionSysInt extends ObtenirQuestionInt {

	function get_question( $question_id ){
		return $this->_source->get_question_sys_dao()->get_question( $question_id );
	}	

}
