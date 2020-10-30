<?php

require_once __DIR__.'/question.php';
require_once 'domaine/entités/question_prog_eval.php';

class QuestionProgDAO extends QuestionDAO{

    static function get_question( $id ){
		$question=new QuestionProgEval( $id );
		QuestionProgEvalDAO::load( $question );
		return $question;
    }
	
    protected static function load( $objet ){
    }
}
