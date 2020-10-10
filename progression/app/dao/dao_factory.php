<?php

require_once('avancement_dao.php');
require_once('question_dao.php');
require_once('question_prog_dao.php');
require_once('serie_dao.php');
require_once('theme_dao.php');
require_once('user_dao.php');

class DAOFactory {

	function get_avancement_dao() {
		return new AvancementDAO();
	}

	function get_question_dao() {
		return new QuestionDAO();
	}

	function get_question_prog_dao() {
		return new QuestionProgDAO();
	}

	function get_série_dao() {
		return new SérieDAO();
	}

	function get_thème_dao() {
		return new ThèmeDAO();
	}

	function get_user_dao() {
		return new UserDAO();
	}
}


?>
