<?php

class ConnexionException extends Exception{}

require_once(__DIR__.'/../../config.php');
require_once('controleur.php');
require_once('domaine/interacteurs/login_interacteur.php');

class ControleurLogin extends Controleur {

	function __construct($source, $réponse_utilisateur){
		parent::__construct($source);
		
		$this->submit=$réponse_utilisateur["submit"];
		$this->username=$réponse_utilisateur["username"];
		$this->passwd=$réponse_utilisateur["passwd"];
	}

	function set_infos_session($user){
		#Obtient les infos de l'utilisateur
		$_SESSION["user_id"]=$user->id;
		$_SESSION["username"]=$user->username;
		$_SESSION["actif"]=$user->actif;
		$_SESSION["role"]=$user->role;
	}

	function récupérer_configs(){
		$configs=array( "domaine_mail"=>$GLOBALS['config']['domaine_mail'],
						"password"=>$GLOBALS['config']['auth_type']!="no"?"true":"");
		return $configs;
	}

	function effectuer_login($username, $password) {
		$user = (new LoginInteracteur($this->_source, $username, $password))->effectuer_login();
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
			array( "template" => "login",
				   "titre" => "Connexion",
				   "erreur" => $this->erreur ),
			$this->récupérer_configs());
	}

}
