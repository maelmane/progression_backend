<?php

require_once __DIR__ . '/entite.php';
require_once __DIR__ . '/question.php';
require_once 'domaine/entités/question.php';

class QuestionDAO extends EntiteDAO
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

    static function get_question($id)
    {
        $question = new Question($id);
        QuestionDAO::load($question);
        return $question;
    }

    static function get_questions_par_série($série_id, $inactif = false)
    {
        $res = [];
        foreach (
            SérieDAO::get_questions_ids($série_id, $inactif)
            as $question_id
        ) {
            $res[] = QuestionDAO::get_question($question_id);
        }
        return $res;
    }

    protected static function load($objet)
    {
        $query = QuestionDAO::$conn->prepare('SELECT question.questionID,
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
