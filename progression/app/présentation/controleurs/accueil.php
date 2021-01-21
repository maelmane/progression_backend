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

require_once __DIR__ . '/controleur.php';
require_once 'domaine/interacteurs/obtenir_theme.php';

class AccueilCtl extends Controleur
{
    function __construct($source, $user_id)
    {
        parent::__construct($source, $user_id);

        $interacteur = new ObtenirThèmeInt($this->_source, $this->_user_id);
        $this->_thèmes = $interacteur->get_thèmes();
    }

    function get_page_infos()
    {
        return array_merge(parent::get_page_infos(), [
            "themes" => $this->_thèmes,
            "titre" => "Taleau de bord",
            "template" => "accueil",
        ]);
    }
}

?>
