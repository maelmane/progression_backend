<?php

require_once 'interacteur.php';

require_once 'domaine/entités/user.php';
require_once 'domaine/entités/question.php';

class ObtenirUserInt extends Interacteur {
	function get_user($user_id){
		return $this->_source->get_user_dao()->get_user($user_id);
	}
}

?>
