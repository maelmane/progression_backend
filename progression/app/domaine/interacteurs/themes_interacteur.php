<?php

require_once(__DIR__."/../entités/theme.php");

class ThemeInteracteur {
	private $_theme_dao;
	
	function get_themes($theme_dao, $user){
		return $_theme_dao->get_themes( $user->role == User::ROLE_ADMIN );
	}
}

?>
