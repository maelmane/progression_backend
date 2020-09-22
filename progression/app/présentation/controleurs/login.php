<?php

class ConnexionException extends Exception{}

//session_start();
//require __DIR__ . '/../vendor/autoload.php';
require_once(__DIR__.'/../../config.php');
//require_once(__DIR__.'/modele.php');
require_once(__DIR__.'/../../domaine/entités/user.php');
require_once(__DIR__.'/../../domaine/interacteurs/user_interacteur.php');

//load_config();

//if(isset($_SESSION["user_id"])){
//	header('Location: /index.php?p=accueil');
//} else {
//	$configs=récupérer_configs();
//	if(!isset($_POST["submit"])){
//		render_page($configs, "");
//	}
//	else{
//		try{
//			effectuer_login();
//			rediriger_apres_login();
//		}
//		catch(ConnexionException $e){
//			render_page($configs, $e->getMessage());
//		}
//	}
//}

class ControleurLogin {

	function __construct($réponse_utilisateur){
		$this->submit=$réponse_utilisateur["submit"];
		$this->username=$réponse_utilisateur["username"];
		$this->passwd=$réponse_utilisateur["passwd"];
	}

	function effectuer_login(){
		if($GLOBALS['config']['auth_type']=="no"){
			$user=$this->login_sans_authentification();
		}
		elseif($GLOBALS['config']['auth_type']=="local"){
			$user=$this->login_local();
		}
		elseif($GLOBALS['config']['auth_type']=="ldap"){
			$user=$this->login_ldap();
		}

		$this->get_infos_session($user);
	}

	function login_local(){
		throw new ConnexionException("L'authentification locale n'est pas implémentée.");
	}

	function login_ldap(){
		$this->vérifier_champs_valides();
		$user=$this->get_utilisateur_ldap();
		$user_info=UserInteracteur::obtenir_ou_créer_user(new UserDAO(), $this->username);
		$user_info->nom=$user['cn'][0];

		return $user_info;
	}

	function vérifier_champs_valides(){
		if(empty($_POST["username"]) || empty($_POST["passwd"])){
			throw new ConnexionException("Le nom d'utilisateur ou le mot de passe ne peuvent être vides.");
		}
	}

	function get_utilisateur_ldap(){

		$username=$this->username;
		$password=$this->passwd;

		#Tentative de connexion à AD
		define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);
		
		$ldap = ldap_connect("ldaps://".$GLOBALS['config']['hote_ad'],$GLOBALS['config']['port_ad']) or die("Configuration de serveur LDAP invalide.");
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
		$bind = @ldap_bind($ldap, $GLOBALS['config']['dn_bind'], $GLOBALS['config']['pw_bind']);

		if(!$bind) {
			ldap_get_option($ldap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
			throw new ConnexionException("Impossible de se connecter au serveur d'authentification. Veuillez communiquer avec l'administrateur du site. Erreur : $extended_error");
		}
		$result=ldap_search($ldap, $GLOBALS['config']['domaine_ldap'], "(sAMAccountName=$username)", array('dn','cn',1));
		$user=ldap_get_entries($ldap, $result);

		if(!$user[0] || !@ldap_bind($ldap, $user[0]['dn'], $password)){
			throw new ConnexionException("Nom d'utilisateur ou mot de passe invalide.");
		}
		return $user[0];
	}

	function get_infos_session($user_info){
		#Obtient les infos de l'utilisateur
		$_SESSION["nom"]=$user_info->nom;
		$_SESSION["user_id"]=$user_info->id;
		$_SESSION["username"]=$user_info->username;
		$_SESSION["actif"]=$user_info->actif;
		$_SESSION["role"]=$user_info->role;
	}

	function rediriger_apres_login(){
		if(!isset($_GET["p"])){
			header("Location: /index.php?p=accueil");
		}
		else{
			header("Location: /index.php?p=$_GET[p]&ID=$_GET[ID]");
		}
	}

	function login_sans_authentification(){
		$username=$_POST["username"];
		return UserInteracteur::obtenir_ou_créer_user($username);
	}

	function récupérer_configs(){
		$configs=array( "domaine_mail"=>$GLOBALS['config']['domaine_mail'],
						"password"=>$GLOBALS['config']['auth_type']!="no"?"true":"");
		return $configs;
	}

	function get_page_infos(){
		if ( ! is_null($this->submit) ){
			$this->effectuer_login();
			$this->rediriger_apres_login();
		}

		return merge_array(
			array("template" => "login",
				  "titre" => "Connexion"),
			$this->récupérer_configs());
	}
}
