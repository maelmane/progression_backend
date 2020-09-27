<?php

require_once("interacteur.php");

require_once(__DIR__."/../entités/user.php");
require_once(__DIR__."/../entités/question.php");

class UserInteracteur extends Interacteur {
	function get_user($user_id){
		return $this->_source->get_user_dao()->get_user($user_id);
	}
	
	function obtenir_ou_créer_user($username){
		$user_dao = $this->_source->get_user_dao();
		
		if($user_dao->existe($username)){
			$user=$user_dao->trouver_par_nomusager($username);
		}
		else{
			$user=$user_dao->créer($username);
		}

		return $user;
	}

    function get_pourcentage_avancement($user_id, $theme_id){
		return floor(UserInteracteur::get_avancement($user_id, $theme_id)/$this->_source->get_theme_dao()->get_nb_questions_actives($theme_id)*100);
    }

	static function get_avancement($user_id, $theme_id){
		$query=ThemeDAO::$conn->prepare('SELECT count(question.questionID) FROM avancement, question, serie WHERE 
	                                     avancement.questionID=question.questionID AND 
	                                     avancement.userID= ? AND 
	                                     question.serieID=serie.serieID AND 
	                                     serie.themeID= ? AND
	                                     question.actif = 1 AND
	                                     serie.actif = 1 AND
	                                     avancement.etat = '.Question::ETAT_REUSSI);
		$query->bind_param( "ii", $user_id, $theme_id);
		$query->execute();
		$query->bind_result($res);
		$query->fetch();
		$query->close();

		return $res;
	}

}

?>
