<?php

require_once 'domaine/entités/question.php';
require_once __DIR__.'/interacteur.php';

class SauvegarderQuestionInt extends Interacteur {

	function __construct($source, $user_id) {
		parent::__construct($source);
		$this->_user_id=$user_id;
	}

	public function set_avancement_réussi($question_id, $code){
		$dao = $this->_source->get_avancement_dao();
		$avancement = $dao->get_avancement($question_id, $this->_user_id);
		$avancement->code = $code;
		$avancement->etat = Question::ETAT_REUSSI;
		$dao->save($avancement);
	}

	public function set_avancement_échec($question_id, $code){
		$dao = $this->_source->get_avancement_dao();
		$avancement = $dao->get_avancement($question_id, $this->_user_id);
		
		if($avancement->etat!=Question::ETAT_REUSSI){
			$avancement->code = $code;
			$avancement->etat = Question::ETAT_NONREUSSI;
			$dao->save($avancement);
		}
	}
	
}
