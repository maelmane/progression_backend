<?php

require_once __DIR__.'/interacteur.php';
require_once __DIR__.'/obtenir_question.php';

class ObtenirSérieInt extends Interacteur {

	function __construct( $source, $user_id ) {
		parent::__construct( $source );
		$this->_user_id=$user_id;
	}
	
	function get_série( $série_id ){
		return $this->_source->get_série_dao()->get_série( $série_id );
	}	
	
	function get_séries_par_thème( $thème_id ){
		$séries=$this->_source->get_série_dao()->get_séries_par_thème( $thème_id );
		$this->calculer_avancement( $séries );

		return $séries;
	}

	private function calculer_avancement( $séries ){
		foreach( $séries as $série ){
			$série->avancement=$this->get_pourcentage_avancement( $série->id );
		}
	}

	function get_pourcentage_avancement( $série_id ){
		$dao = $this->_source->get_série_dao();
		$nb_questions_réussies = $dao->get_avancement( $série_id, $this->_user_id );
		$nb_questions_total = $dao->get_nb_questions_actives( $série_id );

        return $nb_questions_total == 0 ? 0 : floor( $nb_questions_réussies / $nb_questions_total *100 );
    }


}
