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

require_once __DIR__ . '/interacteur.php';
require_once __DIR__ . '/obtenir_question.php';

class ObtenirSérieInt extends Interacteur
{
    function __construct($source, $user_id)
    {
        parent::__construct($source);
        $this->_user_id = $user_id;
    }

    function get_série($série_id)
    {
        return $this->_source->get_série_dao()->get_série($série_id);
    }

    function get_séries_par_thème($thème_id)
    {
        $séries = $this->_source
            ->get_série_dao()
            ->get_séries_par_thème($thème_id);
        $this->calculer_avancement($séries);

        return $séries;
    }

    private function calculer_avancement($séries)
    {
        foreach ($séries as $série) {
            $série->avancement = $this->get_pourcentage_avancement($série->id);
        }
    }

    function get_pourcentage_avancement($série_id)
    {
        $dao = $this->_source->get_série_dao();
        $nb_questions_réussies = $dao->get_avancement(
            $série_id,
            $this->_user_id
        );
        $nb_questions_total = $dao->get_nb_questions_actives($série_id);

        return $nb_questions_total == 0
            ? 0
            : floor(($nb_questions_réussies / $nb_questions_total) * 100);
    }
}
