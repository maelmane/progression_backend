<?php

require_once __DIR__.'/interacteur.php';

require_once 'domaine/entités/user.php';
require_once 'domaine/entités/question.php';

class CréerUserInt extends Interacteur {
	
	function obtenir_ou_créer_user( $username ){
		$user_dao = $this->_source->get_user_dao();
		
		$user = $user_dao->trouver_par_nomusager( $username );

		if ( $user == null ) {
			$user=new User( null );
			$user->username = $username;
			$user = $user_dao->save( $user );
		}

		return $user;
	}

}

?>
