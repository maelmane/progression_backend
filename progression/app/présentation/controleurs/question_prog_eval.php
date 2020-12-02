<?php

require_once 'domaine/interacteurs/obtenir_question_prog_eval.php';
require_once __DIR__ . '/question_prog.php';

class QuestionProgEvalCtl extends QuestionProgCtl
{
    public function __construct($source, $user_id, $question_id)
    {
        parent::__construct($source, $user_id, $question_id);
        $this->préparateur = new PréparerProgEvalInt();
    }

    protected function get_question()
    {
        return (new ObtenirQuestionProgEvalInt(
            $this->_source,
            $this->_user_id
        ))->get_question($this->_question_id);
    }

    public function get_page_infos()
    {
        $infos = array_merge(parent::get_page_infos(), [
            "template" => "question_prog_eval",
        ]);

        return $infos;
    }

    protected function récupérer_paramètres()
    {
        $infos = array_merge(parent::récupérer_paramètres(), [
            "première_ligne_éditeur_precode" =>
                $this->compter_lignes($this->question->pre_exec) + 1,
            "première_ligne_éditeur_incode" =>
                $this->compter_lignes($this->question->pre_exec) +
                $this->compter_lignes($this->question->pre_code) +
                1,
            "solution" => $this->resume($this->question->solution, 21),
        ]);

        return $infos;
    }

    protected function get_exécutable($langage_id)
    {
        $exécutable = $this->préparateur->get_exécutable(
            $this->question,
            $this->avancement,
            $this->get_code()
        );

        return $exécutable;
    }

    protected function get_code()
    {
        return isset($_REQUEST["incode"]) ? $_REQUEST["incode"] : "";
    }

    protected function get_tests()
    {
        $test = $this->préparateur->get_test(
            $this->question,
            isset($_REQUEST["params"]) ? $_REQUEST["params"] : null,
            isset($_REQUEST["stdin"]) ? $_REQUEST["stdin"] : null
        );

        return [$test];
    }

    protected function get_langage()
    {
        return isset($_REQUEST["langid"])
            ? $_REQUEST["langid"]
            : $this->question->lang;
    }

    private function compter_lignes($texte)
    {
        if ($texte == "") {
            return 0;
        } else {
            return count(preg_split('/\n/', $texte));
        }
    }
}

?>
