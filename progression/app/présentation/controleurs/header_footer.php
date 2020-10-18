<?php

require_once 'domaine/entités/user.php';
require_once 'domaine/interacteurs/obtenir_user.php';
require_once 'domaine/interacteurs/obtenir_theme.php';
require_once __DIR__.'/controleur.php';

class HeaderFooterCtl extends Controleur {
	function __construct( $source, $user_id ){
		parent::__construct( $source, $user_id );
		$this->_user_id = $user_id;
	}
	
	function get_header_infos( $thèmeID ){
		$user=( new ObtenirUserInt( $this->_source ))->get_user( $this->_user_id );
		$interacteur = new ObtenirThèmeInt( $this->_source, $this->_user_id );
		$thèmes=$interacteur->get_thèmes( $user );

		if ( ! is_null( $thèmeID ) ) {
			foreach ( $thèmes as $thème ) {
				if ( $thème->id == $thèmeID ) $thème->courant="true";
			}
		}

		$infos=array( parent::get_page_infos(),
					 "username"=>$user->username,
					 "themes"=>$thèmes,
					 "est_admin"=>$user->role == User::ROLE_ADMIN,
					 "dashboard_actif"=>$thèmes[ 0 ]->actif );

		return $infos;
	}

}
?>
