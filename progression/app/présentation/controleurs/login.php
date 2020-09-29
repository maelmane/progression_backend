<?php

class ConnexionException extends Exception{}

require_once(__DIR__.'/../../config.php');
require_once('controleur.php');

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

	function get_page_infos(){
		if ( ! is_null($this->submit) ){
			$user = $this->(new LoginInteracteur($this->_source))->effectuer_login();
			$this->set_infos_session($user);
		}

		return array_merge(
			array("template" => "login",
				  "titre" => "Connexion"),
			$this->récupérer_configs());
	}

}
