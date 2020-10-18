<?php

class Controleur{
	
	function __construct( $source, $user_id ){
		$this->_source = $source;
		$this->_user_id = $user_id;
		$this->_erreur = null;
	}

	function get_page_infos() {
		return array( "erreur" => $this->_erreur );
	}
}
?>
