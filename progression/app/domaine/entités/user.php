<?php

require_once("entite.php");

class User extends Entite{
	
	const ROLE_NORMAL=0;
	const ROLE_ADMIN=1; 
	
	public $username;
	public $role;
	public $id; 

}

?>
