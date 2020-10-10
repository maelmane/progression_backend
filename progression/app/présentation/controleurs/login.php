<?php

class ConnexionException extends Exception{}

require_once 'config.php';
require_once __DIR__.'/controleur.php';
require_once 'domaine/interacteurs/login.php';

class LoginCtl extends Controleur {

	function __construct($source, $réponse_utilisateur){
		parent::__construct($source, null);
		
		$this->submit=$réponse_utilisateur["submit"];
		$this->username=$réponse_utilisateur["username"];
		$this->passwd=$réponse_utilisateur["passwd"];
	}

	private function set_infos_session($user){
		#Obtient les infos de l'utilisateur
		$_SESSION["user_id"]=$user->id;
		$_SESSION["username"]=$user->username;
		$_SESSION["actif"]=$user->actif;
		$_SESSION["role"]=$user->role;
	}

	private function récupérer_configs(){
		$configs=array( "domaine_mail"=>$GLOBALS['config']['domaine_mail'],
						"password"=>$GLOBALS['config']['auth_type']!="no"?"true":"");
		return $configs;
	}

	function effectuer_login($username, $password) {
		$user = (new LoginInt($this->_source, $username, $password))->effectuer_login();
		if ( $user != null ) {
			$this->set_infos_session($user);
		}
		else {
			$this->erreur = "Nom d'utilisateur ou mot de passe invalide";
		}

		return $user;
	}

	function get_page_infos(){
		return array_merge(
			parent::get_page_infos(),
			array( "template" => "login",
				   "titre" => "Connexion"),
			$this->récupérer_configs());
	}

}
