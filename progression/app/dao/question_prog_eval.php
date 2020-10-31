<?php

require_once __DIR__ . '/question.php';
require_once 'domaine/entités/question_prog_eval.php';

class QuestionProgEvalDAO extends QuestionDAO
{
    static function get_question($id)
    {
        $question = new QuestionProgEval($id);
        QuestionProgEvalDAO::load($question);
        return $question;
    }

    protected static function load($objet)
    {
        parent::load($objet);
        $query = QuestionProgEvalDAO::$conn
            ->prepare('SELECT question_prog.lang, 
                                                theme.lang, 
                                                question_prog.setup, 
                                                question_prog.pre_exec, 
                                                question_prog.pre_code, 
                                                question_prog.in_code, 
                                                question_prog.post_code, 
                                                question_prog.solution, 
                                                question_prog.params, 
                                                question_prog.stdin
                                                FROM question 
                                                JOIN question_prog ON
                                                question.questionID=question_prog.questionID 
                                                JOIN serie ON
                                                question.serieID=serie.serieID 
                                                JOIN theme ON
                                                serie.themeID=theme.themeID
                                                WHERE question.questionID = ?');

        $query->bind_param("i", $objet->id);
        $query->execute();
        $query->bind_result(
            $qlang,
            $tlang,
            $objet->setup,
            $objet->pre_exec,
            $objet->pre_code,
            $objet->incode,
            $objet->post_code,
            $objet->solution,
            $objet->params,
            $objet->stdin
        );
        if (is_null($query->fetch())) {
            $objet->id = null;
        }
        if (is_null($qlang) || $qlang == -1) {
            $objet->lang = $tlang;
        } else {
            $objet->lang = $qlang;
        }
        $query->close();
    }

    public static function save($objet)
    {
        if (!$objet->id) {
            $qid = parent::save($objet);
            $query = QuestionProgEvalDAO::$conn
                ->prepare("INSERT INTO question_prog( questionID,
                                                    lang,
                                                    setup,
                                                    pre_exec,
                                                    pre_code,
                                                    in_code,
                                                    post_code,
                                                    reponse,
                                                    params,
                                                    stdin )
                                                    VALUES( $qid, ?, ?, ?, ?, ?, ?, ?, ?, ? )");
            $query->bind_param(
                "issssssss",
                $objet->lang,
                $objet->setup,
                $objet->pre_exec,
                $objet->pre_code,
                $objet->incode,
                $objet->post_code,
                $objet->reponse,
                $objet->params,
                $objet->stdin
            );
            $query->execute();
            $query->close();

            $objet->id = mysqli_insert_id();
        } else {
            $qid = parent::save($objet);
            $query = QuestionProgEvalDAO::$conn
                ->prepare("UPDATE question_prog SET lang=?,
                                                    setup=?,
                                                    pre_exec=?,
                                                    pre_code=?,
                                                    in_code=?,
                                                    post_code=?,
                                                    reponse=?,
                                                    params=?,
                                                    stdin=? 
                                                    WHERE questionID=$qid");
            $query->bind_param(
                "issssssss",
                $objet->lang,
                $objet->setup,
                $objet->pre_exec,
                $objet->pre_code,
                $objet->incode,
                $objet->post_code,
                $objet->reponse,
                $objet->params,
                $objet->stdin
            );
            $query->execute();
            $query->close();
        }

        return $objet;
    }
}
