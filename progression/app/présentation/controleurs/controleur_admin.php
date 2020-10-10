<?php

require_once 'controleur.php';
require_once 'domaine/entités/user.php';

class ControleurAdmin extends Controleur {

	function __construct($id, $user_id){
		parent::__construct($id, $user_id);

		$user=new User($user_id);
		if ($user->role != User::ROLE_ADMIN){
			http_response_code(403);
			die('Forbidden');
		}
	}
}
