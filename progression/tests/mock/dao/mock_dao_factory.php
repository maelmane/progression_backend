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
namespace progression\dao;

class DAOFactory
{
    function get_avancement_dao()
    {
        return new MockAvancementDAO();
    }

    function get_avancement_prog_dao()
    {
        return new MockAvancementProgDAO();
    }

    function get_avancement_sys_dao()
    {
        return new MockAvancementSysDAO();
    }

    function get_question_dao()
    {
        return new MockQuestionDAO();
    }

    function get_question_prog_eval_dao()
    {
        return new MockQuestionProgEvalDAO();
    }

    function get_question_prog_dao()
    {
        return new MockQuestionProgDAO();
    }

    function get_question_sys_dao()
    {
        return new MockQuestionSysDAO();
    }

    function get_série_dao()
    {
        return new MockSérieDAO();
    }

    function get_thème_dao()
    {
        return new MockThèmeDAO();
    }

    function get_user_dao()
    {
        return new MockUserDAO();
    }
}

?>
