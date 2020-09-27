<?php

require_once("domaine/entités/user.php");

class UserDAO extends EntiteDAO{

	public function existe($username){
		return !is_null($this->trouver_par_nomusager($username));
	}

	public function trouver_par_nomusager($username){
		$query=$GLOBALS["conn"]->prepare( 'SELECT userID FROM users WHERE username = ?');
		$query->bind_param( "s", $username );
		$query->execute();
		$query->bind_result( $id );
		$res=$query->fetch();
		$query->close();

		$user = new User($id);
		$this->load($user);

		return $user;
	}

	public function get_user($user_id) {
		$user = new User($user_id);
		UserDAO::load($user);

		return $user;
	}
	
	public function load($objet){
		$query=$GLOBALS["conn"]->prepare( 'SELECT userID, username, actif, role FROM users WHERE userID = ? ');
		$query->bind_param( "i", $objet->id);
		$query->execute();
		
		$query->bind_result( $objet->id, $objet->username, $objet->actif, $objet->role );
		$res=$query->fetch();
		$query->close();
	} 

	public function créer($username){
		$query=$GLOBALS["conn"]->prepare('INSERT INTO users(username) VALUES (?)');
		$query->bind_param( "s", $username);
		$query->execute();
		$query->close();
		
		return $this->trouver_par_nomusager($username); 
	}
}
?>
