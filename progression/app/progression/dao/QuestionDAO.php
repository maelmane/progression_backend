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

use progression\domaine\entité\Question;

class QuestionDAO extends EntitéDAO
{
    static function get_type($id)
    {
        $query = QuestionDAO::$conn->prepare(
            'SELECT type FROM question WHERE questionID = ?'
        );
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($type);
        if (is_null($query->fetch())) {
            error_log($query->error);
            $type = null;
        }
        $query->close();

        return $type;
    }

    public function get_question($id)
    {
        $question = new Question($id);
        $this->load($question);
        return $question;
    }

    public function get_questions_par_série($série_id, $inactif = false)
    {
        $res = [];
        $série = new SérieDAO();
        foreach (
            $série->get_questions_ids($série_id, $inactif)
            as $question_id
        ) {
            $res[] = $this->get_question($question_id);
        }
        return $res;
    }

    protected function load($objet)
    {
        $query = $this->conn->prepare('SELECT question.questionID,
	                                        question.actif,
	                                        question.type,
	                                        question.serieID as s,
	                                        question.numero as n,
	                                        ( select questionID from question where serieID=s and numero=n+1 ) as suivante,
	                                        question.titre,
	                                        question.description,
	                                        question.enonce,
	                                        question.feedback_pos,
	                                        question.feedback_neg,
	                                        question.code_validation
	                                        FROM question
	                                        WHERE question.questionID = ?');
        $query->bind_param("i", $objet->id);
        $query->execute();
        $query->bind_result(
            $objet->id,
            $objet->actif,
            $objet->type,
            $objet->serieID,
            $objet->numero,
            $objet->suivante,
            $objet->titre,
            $objet->description,
            $objet->enonce,
            $objet->feedback_pos,
            $objet->feedback_neg,
            $objet->code_validation
        );
        if (is_null($query->fetch())) {
            error_log($query->error);
            $objet->id = null;
        }
        $query->close();
    }

    static function save($objet)
    {
        if (!$objet->id) {
            $query = QuestionDAO::$conn->prepare("INSERT INTO question( serieID,
	                                                          actif,
	                                                          type,
	                                                          titre,
	                                                          description,
	                                                          numero,
	                                                          enonce,
	                                                          feedback_pos,
	                                                          feedback_neg,
	                                                          code_validation ) 
	                                 VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");

            $query->bind_param(
                "iiississss",
                $objet->serieID,
                $objet->actif,
                $objet->type,
                $objet->titre,
                $objet->description,
                $objet->numero,
                $objet->enonce,
                $objet->feedback_pos,
                $objet->feedback_neg,
                $objet->code_validation
            );
            $query->execute();
            $query->close();

            $objet->id = mysqli_insert_id();
        } else {
            $query = QuestionDAO::$conn->prepare("UPDATE question set 
	                                            serieID=?,
	                                            actif=?,
	                                            type=?,
	                                            titre=?,
	                                            description=?,
	                                            numero=?,
	                                            enonce=?,
	                                            feedback_pos=?,
	                                            feedback_neg=?,
	                                            code_validation=? WHERE questionID = ?");

            $query->bind_param(
                "iiississssi",
                $objet->serieID,
                $objet->actif,
                $objet->type,
                $objet->titre,
                $objet->description,
                $objet->numero,
                $objet->enonce,
                $objet->feedback_pos,
                $objet->feedback_neg,
                $objet->code_validation,
                $objet->id
            );
            $query->execute();
            $query->close();
        }

        return $objet;
    }
}
