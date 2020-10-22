<?php

require_once 'domaine/entités/avancement.php';
require_once __DIR__.'/interacteur.php';

class SauvegarderConteneurInt extends Interacteur {

	function __construct( $source, $user_id ) {
		parent::__construct( $source );
		$this->_user_id=$user_id;
	}

	function sauvegarder_conteneur( $avancement, $conteneur_id ) {
		$dao = $this->_source->get_avancement_dao();
		$avancement->conteneur = $conteneur_id;
		$dao->save( $avancement );
	}
}
