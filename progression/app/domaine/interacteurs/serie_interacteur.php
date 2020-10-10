<?php

require_once("domaine/entités/serie.php");
require_once("domaine/interacteurs/interacteur.php");
require_once("domaine/interacteurs/question_interacteur.php");

class SérieInteracteur extends Interacteur {

	function __construct($source, $user_id) {
		parent::__construct($source);
		$this->_user_id=$user_id;
	}
	
	function get_série($série_id){
		return $this->_source->get_série_dao()->get_série($série_id);
	}	
	
	function get_questions($série_id){
		$série = new Série($série_id);
		$questions = $this->_source->get_série_dao()->get_questions($série_id);

		$this->calculer_réussite($questions);

		return $questions;
	}

	function calculer_réussite($questions){
		$interacteur = new QuestionInteracteur( $this->_source, $this->_user_id );
		
		foreach($questions as $question){
			$question->réussie=$interacteur->get_avancement( $question->id )->etat == Question::ETAT_REUSSI;
		}
	}

	function get_pourcentage_avancement($série_id){
		$dao = $this->_source->get_série_dao();
		$nb_questions = $dao->get_nb_questions_actives($série_id);

        return $nb_questions == 0 ? 0 : floor($dao->get_avancement($série_id, $this->_user_id)/$nb_questions*100);
    }


}
