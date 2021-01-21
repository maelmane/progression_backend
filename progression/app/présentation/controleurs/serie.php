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
require_once 'domaine/interacteurs/obtenir_serie.php';
require_once 'domaine/interacteurs/obtenir_question.php';

class SérieCtl extends Controleur
{
    function __construct($source, $série_id, $user_id)
    {
        parent::__construct($source, $user_id);

        $this->_série = (new ObtenirSérieInt(
            $this->_source,
            $user_id
        ))->get_série($série_id);
        $this->_questions = (new ObtenirQuestionInt(
            $this->_source,
            $user_id
        ))->get_questions_par_série($série_id);

        $thème_id = $this->_série->thème_id;
        $this->_thème = (new ObtenirThèmeInt(
            $this->_source,
            $user_id
        ))->get_thème($thème_id);
    }

    function get_page_infos()
    {
        return array_merge(parent::get_page_infos(), [
            "template" => "serie",
            "serie" => $this->_série,
            "titre" => $this->_thème->titre,
            "questions" => $this->_questions,
        ]);
    }
}
