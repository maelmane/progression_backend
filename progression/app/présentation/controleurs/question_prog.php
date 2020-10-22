<?php

require_once __DIR__.'/prog.php';
require_once 'domaine/interacteurs/obtenir_avancement.php';
require_once 'domaine/interacteurs/obtenir_question_prog.php';
require_once 'domaine/interacteurs/obtenir_serie.php';
require_once 'domaine/interacteurs/executer_prog.php';
require_once 'domaine/interacteurs/traiter_resultats_prog.php';

class QuestionProgCtl extends ProgCtl{

	function __construct( $source, $user_id, $question_id ){
		parent::__construct( $source, $user_id );

		$this->_question_id = $question_id;

		$this->question = ( new ObtenirQuestionProgInt( $this->_source, $user_id ) )->get_question( $this->_question_id );
		
		$this->avancement = ( new ObtenirAvancementInt( $this->_source, $user_id ) )->get_avancement( $this->_question_id, $this->_question_id );

		$this->série = ( new ObtenirSérieInt( $source, $user_id ) )->get_série( $this->question->serieID );
	}

	protected function get_code( $question, $avancement ) {
		if ( $this->incode != null ) {
			return $this->incode;
		}
		elseif ( $avancement->code!=null ) {
			return $avancement->code;
		}
		elseif ( $question->code!=null ) {
			return $question->code;
		}
		else {
			return "";
		}
	}
	
	protected function get_params( $question, $params ){
		if ( !is_null( $question ) && $question->params!="" ){
			return $question->params;
		}
		elseif ( $params!=null ){
			return $this->params;
		}
		else {
			return "";
		}
	}

	protected function get_stdin( $question, $stdin ){
		if ( !is_null( $question ) && $question->stdin!="" ){
			return $question->stdin;
		}
		elseif ( $stdin!=null ){
			return $this->stdin;
		}
		else {
			return "";
		}

	}
	
	function get_page_infos(){		
		$infos=array_merge( parent::get_page_infos(),
							array( "template"=>"question_prog" ),
							$this->récupérer_paramètres() );

		if ( $this->à_valider ) {
			$sorties = ( new ExécuterProgInt( $this->_source, $this->_user_id ) )->exécuter( $infos["exécutable"] );

			$infos[ "résultats" ] = ( new TraiterRésultatsProgInt( $this->_source, $this->_user_id ) )->traiter_résultats( $sorties, $this->question, $infos[ "exécutable" ]->code );
			
			$infos = array_merge( $infos, $this->calculer_sorties( $sorties ) );

		}
		$infos[ "solution" ]=$this->resume( $this->question->solution, 21 );

		return $infos;
	}

	private function récupérer_paramètres(){
		eval( $this->question->setup );

		$this->question->énoncé=str_replace( "\r","",eval( "return \"" . $this->question->enonce . "\";" ) );
		$this->question->solution=str_replace( "\r","",eval( "return " . $this->question->solution . ";" ) );
		$this->question->pre_exec=str_replace( "\r","",eval( "return " . $this->question->pre_exec . ";" ) );
		$this->question->pre_code=str_replace( "\r","",eval( "return " . $this->question->pre_code . ";" ) );
		$this->question->post_code=str_replace( "\r","",eval( "return " . $this->question->post_code . ";" ) );

		$exécutable = new class {};
		$exécutable->langid=$this->question->lang;
		$exécutable->pre_exec=$this->question->pre_exec;
		$exécutable->pre_code=$this->question->pre_code;
		$exécutable->code=$this->get_code( $this->question, $this->avancement );
		$exécutable->post_code=$this->question->post_code;
		$exécutable->params=$this->get_params( $this->question, $this->params );
		$exécutable->stdin=$this->get_stdin( $this->question, $this->stdin );
		
		$infos=array( "question"=>$this->question,
					  "titre"=>$this->série->titre,
					  "première_ligne_éditeur_precode"=>$this->compter_lignes( $this->question->pre_exec )+1,
					  "première_ligne_éditeur_incode"=>$this->compter_lignes( $this->question->pre_exec )+$this->compter_lignes( $this->question->pre_code )+1,
					  //Évalue seulement si stdin provient de la BD
					  "url_retour"=>"index.php?p=serie&ID=".$this->question->serieID,
					  "titre_retour"=>"la liste de questions",
					  "état_réussi"=>$this->avancement->etat==Question::ETAT_REUSSI,
					  "mode"=>$this->get_mode( $this->question->lang ),
					  "lang_nom"=>ProgCtl::LANG_NOMS[ $this->question->lang ],
					  "exécutable"=>$exécutable
		);

		return $infos;
	}

	private function compter_lignes( $texte ){
		if ( $texte=="" ){
			return 0;
		}
		else{
			return count( preg_split( '/\n/',$texte ) );
		}
	}
}

?>
