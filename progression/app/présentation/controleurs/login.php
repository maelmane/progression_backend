<?php

class ConnexionException extends Exception{}

require_once 'config.php';
require_once __DIR__.'/controleur.php';
require_once 'domaine/interacteurs/login.php';

class LoginCtl extends Controleur {

	function __construct( $source ){
		parent::__construct( $source, null );
		
		$this->submit = isset( $_REQUEST[ "submit" ] );
		$this->username = ( isset( $_REQUEST[ "username" ] ) ? $_REQUEST[ "username" ] : null );
		$this->passwd = ( isset( $_REQUEST[ "passwd" ] ) ? $_REQUEST[ "passwd" ] : null );
	}
	
	private function set_infos_session( $user ){
		#Obtient les infos de l'utilisateur
		$_SESSION[ "user_id" ]=$user->id;
		$_SESSION[ "username" ]=$user->username;
		$_SESSION[ "actif" ]=$user->actif;
		$_SESSION[ "role" ]=$user->role;
	}

	private function récupérer_configs(){
		$configs=array( "domaine_mail"=>$GLOBALS[ 'config' ][ 'domaine_mail' ],
						"password"=>$GLOBALS[ 'config' ][ 'auth_type' ]!="no"?"true":"" );
		return $configs;
	}

	function effectuer_login( $username, $password ) {
		$user = ( new LoginInt( $this->_source ) )->effectuer_login( $username, $password );
		
		if ( $user != null ) {
			$this->set_infos_session( $user );
		}

		return $user;
	}

	function get_page_infos(){

		$erreurs = null;
		
		if ( isset( $_REQUEST[ "submit" ] ) ) {
			$user = $this->effectuer_login( $_REQUEST[ "username" ], isset( $_REQUEST[ "password" ] ) ? $_REQUEST[ "password" ] : null );
			
			if ( isset( $user ) && $user != null ) {
				return ( new AccueilCtl( new DAOFactory(), $user->id ) )->get_page_infos();
			}
			else {
				$erreurs = "Nom d'utilisateur ou mot de passe invalide.";
			}
		}

		return array_merge( 
			parent::get_page_infos(),
			array( "template" => "login",
				   "titre" => "Connexion",
				   "erreurs" => $erreurs ),
			$this->récupérer_configs() );
	}
}
