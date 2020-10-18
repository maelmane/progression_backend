<?php

require_once __DIR__.'/controleur.php';
require_once 'domaine/entités/theme.php';
require_once 'domaine/interacteurs/obtenir_theme.php';

class ThèmeCtl extends Controleur{

	function __construct( $source, $user_id, $thème_id ){
		parent::__construct( $source, $user_id );

		$this->_thème = ( new ObtenirThèmeInt( $this->_source, $this->_user_id ))->get_thème( $thème_id );
		$this->_séries = ( new ObtenirSérieInt( $this->_source, $this->_user_id ))->get_séries_par_thème( $thème_id );
	}
	
	function get_page_infos(){
		return array( parent::get_page_infos(),
					  "template"=>"theme",
					  "titre"=>$this->_thème->titre,
					  'theme'=>$this->_thème,
					  'series'=>$this->_séries );
		
	}

}

?>
