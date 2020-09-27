<?php

require_once('theme_dao.php');

class DAOFactory {

	function get_theme_dao() {
		return new ThemeDAO();
	}

	function get_user_dao() {
		return new UserDAO();
	}
}


?>
