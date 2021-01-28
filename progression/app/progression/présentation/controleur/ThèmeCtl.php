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

namespace progression\présentation\controleur;

use progression\domaine\interacteur\{ObtenirThèmeInt, ObtenirSérieInt};

class ThèmeCtl extends Controleur
{
    function __construct($source, $user_id, $thème_id)
    {
        parent::__construct($source, $user_id);

        $this->_thème = (new ObtenirThèmeInt(
            $this->_source,
            $this->_user_id
        ))->get_thème($thème_id);
        $this->_séries = (new ObtenirSérieInt(
            $this->_source,
            $this->_user_id
        ))->get_séries_par_thème($thème_id);
    }

    function get_page_infos()
    {
        return array_merge(parent::get_page_infos(), [
            "template" => "theme",
            "titre" => $this->_thème->titre,
            'theme' => $this->_thème,
            'series' => $this->_séries,
        ]);
    }
}

?>
