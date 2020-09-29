<?php

require_once("domaine/entités/theme.php");
require_once("domaine/interacteurs/interacteur.php");

class ThèmeInteracteur extends Interacteur {

	function __construct($source, $user_id) {
		parent::__construct($source);
		$this->_user_id=$user_id;
	}
	
	function get_thèmes(){
		$user = $this->_source->get_user_dao()->get_user($this->_user_id);
		return $this->_source->get_thème_dao()->get_thèmes($user->role == User::ROLE_ADMIN );
	}

	function get_thème($thème_id){
		$thème = new Thème($thème_id);
		$this->_source->get_thème_dao()->load($thème);

		return $thème;
	}	
	
	function get_séries($thème_id){
		$séries=$this->_source->get_thème_dao()->get_séries($thème_id);
		//$this->calculer_avancement($séries);

		return $séries;
	}

	function calculer_avancement($séries){
		$interacteur = new SérieInteracteur($this->source, $this->_user_id);
		foreach($séries as $série){
			$série->avancement=$interacteur->get_pourcentage_avancement($this->_user_id);
		}
	}

}

?>
