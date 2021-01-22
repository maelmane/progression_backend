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


require_once 'domaine/entités/serie.php';
require_once 'domaine/interacteurs/obtenir_avancement.php';
require_once __DIR__ . '/interacteur.php';

class ObtenirQuestionInt extends Interacteur
{
    function __construct($source, $user_id)
    {
        parent::__construct($source);
        $this->_user_id = $user_id;
    }

    function get_question($question_id)
    {
        return $this->_source->get_question_dao()->get_question($question_id);
    }

    function get_questions_par_série($série_id)
    {
        $série = new Série($série_id);
        $questions = $this->_source
            ->get_question_dao()
            ->get_questions_par_série($série_id);

        $this->calculer_réussite($questions);

        return $questions;
    }

    private function calculer_réussite($questions)
    {
        $interacteur = new ObtenirAvancementInt(
            $this->_source,
            $this->_user_id
        );

        foreach ($questions as $question) {
            $question->réussie =
                $interacteur->get_avancement($question->id)->etat ==
                Question::ETAT_REUSSI;
        }
    }
}
