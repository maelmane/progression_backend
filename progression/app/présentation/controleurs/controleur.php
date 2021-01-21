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
?><?php

require_once 'domaine/interacteurs/obtenir_user.php';

class Controleur
{
    function __construct($source, $user_id)
    {
        $this->_source = $source;
        $this->_user_id = $user_id;
        $this->_erreurs = [];
    }

    function get_page_infos()
    {
        return [
            "erreurs" => $this->_erreurs,
            "user" => (new ObtenirUserInt($this->_source))->get_user(
                $this->_user_id
            ),
        ];
    }
}
?>
