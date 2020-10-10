<?php

require_once("domaine/entités/question_prog.php");
require_once("domaine/interacteurs/interacteur.php");

class QuestionProgInteracteur extends QuestionInteracteur {

	function get_question($question_id){
		return $this->_source->get_question_prog_dao()->get_question($question_id);
	}	

}
