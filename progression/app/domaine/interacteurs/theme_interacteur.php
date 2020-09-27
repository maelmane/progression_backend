<?php

require_once(__DIR__."/../entités/theme.php");
require_once("interacteur.php");

class ThemeInteracteur extends Interacteur {
	function get_themes($user){
		return $this->_source->get_theme_dao()->get_themes( $user->role == User::ROLE_ADMIN );
	}
}

?>
