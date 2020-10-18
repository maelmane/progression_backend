<?php

require_once 'controleur.php';

class ControleurQuestionSys extends Controleur {

	function __construct( $id, $user_id, $réponse_utilisateur ){
		parent::__construct( $id, $user_id );
		
		$this->à_valider=$réponse_utilisateur[ "à_valider" ];
		$this->reponse=$réponse_utilisateur[ "reponse" ];
		$this->reset=$réponse_utilisateur[ "reset" ];
	}

	function get_page_infos(){
		$infos=array( "template"=>"question_sys" );
		$infos=array_merge( $infos, $this->get_infos() );
		
		$réponse_serveur=$this->connexion_conteneur( $infos );
		$infos=array_merge( $infos, $this->décoder_réponse( $réponse_serveur ));
		
		if( $this->à_valider ){
			$infos[ 'essayé' ]="true";
			$infos=array_merge( $infos, $this->vérifier_réussite( $infos ));
		}
		
		$this->sauvegarder_conteneur( $infos );
		
		return $infos;
	}

	function get_infos(){
		$this->question=$this->charger_question();
		$this->avancement=$this->charger_avancement();

		$infos=array(
			"question"=>$this->question,
			"réponse"=>$this->get_réponse_utilisateur(),
			"avancement"=>$this->avancement,
			"nom_serveur"=>$GLOBALS[ 'config' ][ 'compilebox_hote' ], //TODO changer?
			"url_retour"=>"index.php?p=serie&ID=" . $this->question->serieID,
			"titre_retour"=>"la liste de questions",
			"params_conteneur"=>"-e SIAB_SERVICE=/:" . $this->question->user . ":" . $this->question->user . ":HOME:SHELL" );

		return $infos;
	}

	function charger_question(){
		$question=new QuestionSysteme( $this->id );

		if( is_null( $question->id )){
			header( 'Location: index.php?p=accueil' );
		}

		return $question;
	}

	function charger_avancement(){
		$avancement=new Avancement( $this->id, $this->user_id );

		return $avancement;
	}

	function get_réponse_utilisateur(){
		return $this->reponse!=null ? $this->reponse : "";
	}

	function connexion_conteneur( $infos ){
		$url_rc=$this->get_url_compilebox();
		$options_rc=$this->get_options_compilebox( $this->question, $this->avancement, $infos );

		$context=stream_context_create( $options_rc );
		$comp_resp=file_get_contents( $url_rc, false, $context );

		return $comp_resp;
	}

	function get_url_compilebox(){
		return "http://".$GLOBALS[ 'config' ][ 'compilebox_hote' ].":".$GLOBALS[ 'config' ][ 'compilebox_port' ]."/compile"; //TODO changer?
	}

	function get_options_compilebox( $question, $avancement, $infos ){
		if( $this->avancement->get_etat()==Question::ETAT_DEBUT || $this->reset ){
			$data_rc=$this->get_data_nouveau_conteneur( $infos );
		}
		else{
			$data_rc=$this->get_data_conteneur( $infos );
		}
		
		$options_rc=array( 'http'=> array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query( $data_rc )) );

		return $options_rc;
	}

	function get_data_nouveau_conteneur( $infos ){
		return array( 'language' => 13,
					 'code' => 'reset',
					 'vm_name' => $infos[ 'question' ]->image,
					 'parameters' => $infos[ 'avancement' ]->conteneur,
					 'params_conteneur' => $infos[ 'params_conteneur' ],
					 'stdin' => '',
					 'user' => $infos[ 'question' ]->user );
	}

	function get_data_conteneur( $infos ){
		return array( 'language' => 13,
					 'code' => $this->construire_validation( $infos[ 'question' ] ),
					 'vm_name' => $infos[ 'question' ]->image,
					 'parameters' => $infos[ 'avancement' ]->conteneur,
					 'params_conteneur' => $infos[ 'params_conteneur' ],
					 'stdin' => '',
					 'user' => $infos[ 'question' ]->user );
	}

	function construire_validation( $question ){
		return str_replace( "{reponse}", $this->get_réponse_utilisateur(), $question->verification );
	}

	function décoder_réponse( $réponse ){
		$infos_réponse=array();
		
		$infos_réponse[ "cont_id" ]=trim( json_decode( $réponse, true )[ 'cont_id' ] );
		$infos_réponse[ "cont_ip" ]=trim( json_decode( $réponse, true )[ 'add_ip' ] );
		$infos_réponse[ "cont_port" ]=trim( json_decode( $réponse, true )[ 'add_port' ] );
		$infos_réponse[ "res_validation" ]=trim( json_decode( $réponse, true )[ 'resultat' ] );

		return $infos_réponse;
	}

	function vérifier_réussite( $infos ){
		$réussite=array();
		
		$réussi=$this->vérifier_réponse( $infos );
		if( $réussi ){
			$réussite[ "réussi" ]="true";
			$infos[ "avancement" ]->set_etat( Question::ETAT_REUSSI );
		}
		else{
			$réussite[ "nonréussi" ]="true";
		}

		//récupère l'état d'avancement
		if( $infos[ "avancement" ]->get_etat()==Question::ETAT_REUSSI ){
			$réussite[ "état_réussi" ]="true";
		}
		else{
			$réussite[ "état_réussi" ]="";
		}
		
		return $réussite;
	}

	function sauvegarder_conteneur( $infos ){
		$infos[ "avancement" ]->set_conteneur( $infos[ "cont_id" ] );
	}

	function vérifier_réponse( $infos ){
		$réussi=false;
		
		//validation exécutée
		if( $infos[ 'res_validation' ]!="" && $infos[ 'res_validation' ]=="valide" ){
			$réussi=true;            
		}
		//réponse textuelle
		elseif( !is_null( $infos[ "question" ]->solution_courte ) && $infos[ "question" ]->solution_courte!="" ){
			if( $infos[ 'réponse' ]!='' )
				if( $infos[ 'réponse' ]==$infos[ "question" ]->solution_courte ){
					$réussi=true;
				}
		}
		return $réussi;
	}
}
?>
