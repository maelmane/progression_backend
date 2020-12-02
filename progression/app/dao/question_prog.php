<?php

require_once __DIR__ . '/question.php';
require_once 'domaine/entités/question_prog_eval.php';

class QuestionProgDAO extends QuestionDAO
{
    static function get_question($id)
    {
        $question = new QuestionProg($id);
        QuestionProgDAO::load($question);
        return $question;
    }

    protected static function load($objet)
    {
        parent::load($objet);

        $objet->exécutables = QuestionProgDAO::load_exécutables($objet->id);
        $objet->tests = QuestionProgDAO::load_tests($objet->id);
    }

    private function load_exécutables($id)
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

    private function load_tests($id)
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
