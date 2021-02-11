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

use progression\domaine\entité\{QuestionProg, Exécutable, Test};

class QuestionProgDAO extends QuestionDAO
{
    static function get_question($id)
    {
        $question = new QuestionProg($id);
        QuestionProgDAO::load($question);

        return $question->id ? $question : null;
    }

    protected static function load($objet)
    {
        parent::load($objet);

        $objet->exécutables = QuestionProgDAO::load_exécutables($objet->id);
        $objet->tests = QuestionProgDAO::load_tests($objet->id);
    }

    private static function load_exécutables($id)
    {
        $query = QuestionProgDAO::$conn->prepare(
            'SELECT code, lang FROM executable WHERE questionID=?'
        );
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($code, $lang);

        $exécutables = [];
        while ($query->fetch()) {
            $exécutables[$lang] = new Exécutable($code, $lang);
        }
        $query->close();

        return $exécutables;
    }

    private static function load_tests($id)
    {
        $query = QuestionProgDAO::$conn->prepare(
            'SELECT nom, stdin, params, solution, feedback_pos, feedback_neg FROM test WHERE questionID=?'
        );
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($nom, $stdin, $params, $solution, $feedback_pos, $feedback_neg);

        $tests = [];
        while ($query->fetch()) {
            $tests[] = new Test($nom, $stdin, $solution, $params, $feedback_pos, $feedback_neg);
        }
        $query->close();

        return $tests;
    }
}
