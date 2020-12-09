<?php

require_once __DIR__ . '/prog.php';
require_once 'domaine/interacteurs/obtenir_avancement_prog.php';
require_once 'domaine/interacteurs/formater_md.php';
require_once 'domaine/interacteurs/preparer_prog.php';
require_once 'domaine/interacteurs/obtenir_serie.php';
require_once 'domaine/interacteurs/obtenir_question_prog.php';
require_once 'domaine/interacteurs/executer_prog.php';
require_once 'domaine/interacteurs/traiter_resultats_prog.php';
require_once 'domaine/interacteurs/preparer_prog_eval.php';

class QuestionProgCtl extends ProgCtl
{
    function __construct($source, $user_id, $question_id)
    {
        parent::__construct($source, $user_id);

        $this->_question_id = $question_id;

        $this->question = $this->get_question();

        $this->avancement = (new ObtenirAvancementProgInt(
            $this->_source,
            $user_id
        ))->get_avancement($this->_question_id, $this->_question_id);

        $this->série = (new ObtenirSérieInt($source, $user_id))->get_série(
            $this->question->serieID
        );
    }

    protected function get_question()
    {
        return (new ObtenirQuestionProgInt(
            $this->_source,
            $this->_user_id
        ))->get_question($this->_question_id);
    }

    public function get_page_infos()
    {
        $exécutable = $this->get_exécutable($this->get_id_langage_sélectionné());
        $tests = $this->get_tests();

        if ($this->à_valider) {
            foreach ($tests as $test) {
                $sorties = (new ExécuterProgInt(
                    $this->_source,
                    $this->_user_id
                ))->exécuter($exécutable, $test);

                $test->sorties = $this->calculer_sorties($sorties);
            }

            $résultats = (new TraiterRésultatsProgInt(
                $this->_source,
                $this->_user_id
            ))->traiter_résultats($exécutable, $tests, $this->question);
            $exécutable->résultats = $résultats;
        }

        $infos = array_merge(
            parent::get_page_infos(),
            $this->récupérer_paramètres(),
            [
                "exécutable" => $exécutable,
                "résultats" => $exécutable->résultats,
                "tests" => $tests,
            ]
        );

        return $infos;
    }

    protected function récupérer_paramètres()
    {
        $this->question->enonce = (new FormaterMDInt())->exécuter(
            $this->question->enonce
        );
        $infos = [
            "template" => "question_prog",
            "question" => $this->question,
            "titre" => $this->série->titre,
            "url_retour" => "index.php?p=serie&ID=" . $this->question->serieID,
            "titre_retour" => "la liste de questions",
            "mode" => $this->get_mode($this->get_id_langage_sélectionné()),
            "langid" => $this->get_id_langage_sélectionné(),
            "langages" => $this->get_langages(),
        ];

        return $infos;
    }

    protected function get_exécutable($langage_id)
    {
        return (new PréparerProgInt())->préparer_exécutable(
            $this->question,
            $this->avancement,
            $langage_id,
            $this->à_valider ? $this->incode : null
        );
    }

    protected function get_tests()
    {
        return $this->question->tests;
    }

    protected function get_id_langage_sélectionné()
    {
        if (isset($_REQUEST["langid"]))
            return $_REQUEST["langid"];
        else if(!is_null($this->avancement->lang))
            return $this->avancement->lang;
        else
            return $this->question->exécutables[array_keys($this->question->exécutables)[0]]->lang;
    }

    protected function get_langages()
    {
        $langages = [];
        foreach ($this->question->exécutables as $exécutable) {
            $langages[ProgCtl::LANG_NOMS[$exécutable->lang]] = true;
        }
        return $langages;
    }
}

?>
