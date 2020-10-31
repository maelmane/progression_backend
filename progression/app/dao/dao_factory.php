<?php

require_once __DIR__ . '/avancement.php';
require_once __DIR__ . '/question.php';
require_once __DIR__ . '/question_prog_eval.php';
require_once __DIR__ . '/question_prog.php';
require_once __DIR__ . '/question_sys.php';
require_once __DIR__ . '/serie.php';
require_once __DIR__ . '/theme.php';
require_once __DIR__ . '/user.php';

class DAOFactory
{
    function get_avancement_dao()
    {
        return new AvancementDAO();
    }

    function get_question_dao()
    {
        return new QuestionDAO();
    }

    function get_question_prog_eval_dao()
    {
        return new QuestionProgEvalDAO();
    }

    function get_question_prog_dao()
    {
        return new QuestionProgDAO();
    }

    function get_question_sys_dao()
    {
        return new QuestionSysDAO();
    }

    function get_série_dao()
    {
        return new SérieDAO();
    }

    function get_thème_dao()
    {
        return new ThèmeDAO();
    }

    function get_user_dao()
    {
        return new UserDAO();
    }
}

?>
