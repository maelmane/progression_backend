<?php

class TraiterRésultatsProgInt extends Interacteur {

	function __construct( $source, $user_id ) {
		parent::__construct( $source );
		$this->_user_id=$user_id;
	}

	function traiter_résultats( $sorties, $question, $code ){
		$résultats=array();

		$avancement = ( new ObtenirAvancementInt( $this->_source, $this->_user_id ) )->get_avancement( $question->id, $this->_user_id );
		$avancement->code = $code;
		
		$résultats[ "essayé" ]="true";
		if( $this->vérifier_solution( $sorties, $question->solution ) ){
			$avancement->etat = Question::ETAT_REUSSI;
			$avancement->code = $code;
			$this->sauvegarder_avancement( $avancement );
			$résultats[ "réussi" ]="true";
		}
		else{
			if( $avancement->etat!=Question::ETAT_REUSSI ){
				$avancement->code = $code;
				$avancement->etat = Question::ETAT_NONREUSSI;
				$this->sauvegarder_avancement( $avancement );
			}				
			$résultats[ "nonréussi" ]="true";
		}

		$résultats[ "état_réussi" ]=$avancement->etat==Question::ETAT_REUSSI;

		return $résultats;
	}

	private function vérifier_solution( $sorties, $solution ){
		$sortie_standard = $sorties[ "stdout" ];

		//en PHP, "" == NULL ( arg!!! )
		return $solution!="null" && $sortie_standard==$solution;
	}

	private function sauvegarder_avancement( $avancement ) {
		$interacteur = new SauvegarderAvancementInt( $this->_source, $this->_user_id );
		$interacteur->sauvegarder( $avancement );
	}

}

?>
