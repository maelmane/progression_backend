<?php

class TraiterRésultatsInt extends Interacteur {

	function __construct( $source, $user_id ) {
		parent::__construct( $source );
		$this->_user_id=$user_id;
	}

	function traiter_résultats( $sorties, $question, $code ){
		$résultats=array();

		$résultats[ "essayé" ]="true";
		if( $this->vérifier_solution( $sorties, $question->solution ) ){
			$this->sauvegarder_état_réussi( $question, $code );
			$résultats[ "réussi" ]="true";
		}
		else{
			$this->sauvegarder_état_échec( $question, $code );
			$résultats[ "nonréussi" ]="true";
		}

		$avancement = ( new ObtenirAvancementInt( $this->_source, $this->_user_id ) )->get_avancement( $question->id, $this->_user_id );

		$résultats[ "état_réussi" ]=$avancement->etat==Question::ETAT_REUSSI;

		return $résultats;
	}

	private function vérifier_solution( $sorties, $solution ){
		$sortie_standard = $sorties[ "stdout" ];

		//en PHP, "" == NULL ( arg!!! )
		return $solution!="null" && $sortie_standard==$solution;
	}

	private function sauvegarder_état_réussi( $question, $code ){
		$interacteur = new SauvegarderQuestionInt( $this->_source, $this->_user_id );
		$interacteur->set_avancement_réussi( $question->id, $code );
	}

	private function sauvegarder_état_échec( $question, $code ){
		$interacteur = new SauvegarderQuestionInt( $this->_source, $this->_user_id );
		$interacteur->set_avancement_échec( $question->id, $code );
	}

}

?>
