<?php

require_once __DIR__.'/interacteur.php';

class ObtenirAvancementInt extends Interacteur {

	function __construct($source, $user_id) {
		parent::__construct($source);
		$this->_user_id=$user_id;
	}

	function get_avancement($question_id){
		return $this->_source->get_avancement_dao()->get_avancement($question_id, $this->_user_id);
	}

}
