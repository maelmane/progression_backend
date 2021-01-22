<?php
/*
  This file is part of Progression.

  Progression is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Progression is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Progression.  If not, see <https://www.gnu.org/licenses/>.
*/


require_once __DIR__ . '/avancement.php';
require_once __DIR__ . '/avancement_prog.php';
require_once __DIR__ . '/avancement_sys.php';
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

    function get_avancement_prog_dao()
    {
        return new AvancementProgDAO();
    }

    function get_avancement_sys_dao()
    {
        return new AvancementSysDAO();
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
