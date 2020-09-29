<?php

require_once('theme_dao.php');
require_once('user_dao.php');

class DAOFactory {

	function get_thème_dao() {
		return new ThèmeDAO();
	}

	function get_user_dao() {
		return new UserDAO();
	}
}


?>
