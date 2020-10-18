<?php

class LogoutCtl extends Controleur {

	function get_page_infos(){
		unset( $_SESSION[ "user_id" ] );
		header( "Location: index.php" );
	}

}
?>
