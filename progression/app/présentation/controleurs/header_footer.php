<?php

require_once('domaine/entités/user.php');
require_once('domaine/interacteurs/theme_interacteur.php');
require_once('controleur.php');

class HeaderControleur extends Controleur {
	function __construct($source, $user_id){
		parent::__construct($source);
		$this->_user_id = $user_id;
	}
	
	function get_header_infos($thèmeID){
		$user=(new UserInteracteur($this->_source))->get_user($this->_user_id);
		$interacteur = new ThemeInteracteur($this->_source);
		$themes=$interacteur->get_themes($user);

		if ( ! is_null($thèmeID) ) {
			$themes[$thèmeID]->courant="true";
		}

		$infos=array("username"=>$user->username,
					 "themes"=>$themes,
					 "est_admin"=>$user->role == User::ROLE_ADMIN,
					 "dashboard_actif"=>$themes[0]->actif);

		return $infos;
	}

}
?>
