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


require_once __DIR__ . '/interacteur.php';

require_once 'domaine/entités/user.php';
require_once 'domaine/entités/question.php';

class ObtenirUserInt extends Interacteur
{
    function get_user_par_nomusager($username)
    {
        return $this->_source->get_user_dao()->trouver_par_nomusager($username);
    }
    function get_user($user_id)
    {
        return $this->_source->get_user_dao()->get_user($user_id);
    }
}

?>
