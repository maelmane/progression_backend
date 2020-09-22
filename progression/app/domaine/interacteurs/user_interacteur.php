<?php

require_once(__DIR__."/../entités/user.php");

class UserInteracteur {
	function obtenir_ou_créer_user($user_dao, $username){

		if($user_dao->existe($username)){
			$user=$user_dao->trouver_par_nomusager($username);
		}
		else{
			$user=$user_dao->créer($username);
		}

		return $user;
	}
}

?>
