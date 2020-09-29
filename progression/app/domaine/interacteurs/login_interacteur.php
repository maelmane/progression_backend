<?php

require_once(__DIR__.'/../../config.php');
require_once('user_interacteur.php');

class LoginInteracteur extends Interacteur {

	function __construct($source, $username, $password){
		$this->_source = $source;
		$this->_username = $username;
		$this->_pasword = $password;
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
	}

	function login_local(){
		throw new ConnexionException("L'authentification locale n'est pas implémentée.");
	}

	function login_ldap(){
		$this->vérifier_champs_valides();
		$user=$this->get_username_ldap();
		$user_info=new UserInteracteur(new DAOFactory(), obtenir_ou_créer_user($this->_username));
		$user_info->nom=$user['cn'][0];

		return $user_info;
	}

	function vérifier_champs_valides(){
		if(empty(trim($this->_username) || empty($this->_password)){
			throw new ConnexionException("Le nom d'utilisateur ou le mot de passe ne peuvent être vides.");
		}
		}

			function get_username_ldap(){
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
				$result=ldap_search($ldap, $GLOBALS['config']['domaine_ldap'], "(sAMAccountName=$this->_username)", array('dn','cn',1));
				$user=ldap_get_entries($ldap, $result);

				if(!$user[0] || !@ldap_bind($ldap, $user[0]['dn'], $this->_password)){
					throw new ConnexionException("Nom d'utilisateur ou mot de passe invalide.");
				}
				return $user[0];
			}

			function login_sans_authentification(){
				$interacteur = new UserInteracteur($this->_source);
				return $interacteur->obtenir_ou_créer_user($this->_source->get_user_dao(), $this->_username);
			}


		}

?>
