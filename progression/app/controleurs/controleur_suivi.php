<?php

require_once('controleur_admin.php');

class ControleurSuivi extends ControleurAdmin {

	function __construct($id, $user_id, $réponse_utilisateur){
		parent::__construct($id, $user_id);
	}
}
