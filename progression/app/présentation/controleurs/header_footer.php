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

require_once 'domaine/entités/user.php';
require_once 'domaine/interacteurs/obtenir_user.php';
require_once 'domaine/interacteurs/obtenir_theme.php';
require_once __DIR__ . '/controleur.php';

class HeaderFooterCtl extends Controleur
{
    function __construct($source, $user_id)
    {
        parent::__construct($source, $user_id);
        $this->_user_id = $user_id;
    }

    function get_header_infos($thèmeID)
    {
        $interacteur = new ObtenirThèmeInt($this->_source, $this->_user_id);
        $thèmes = $interacteur->get_thèmes();

        if (!is_null($thèmeID)) {
            foreach ($thèmes as $thème) {
                if ($thème->id == $thèmeID) {
                    $thème->courant = "true";
                }
            }
        }

        return array_merge(parent::get_page_infos(), [
            "themes" => $thèmes,
            //"est_admin" => $user->role == User::ROLE_ADMIN,
            "dashboard_actif" => $thèmes[0]->actif,
        ]);
    }
}
?>
