<?php

class ControleurLogout extends Controleur {

	function __construct(){
	}

	function get_page_infos(){

		unset($_SESSION["user_id"]);
		header("Location: index.php");

	}

}
?>
