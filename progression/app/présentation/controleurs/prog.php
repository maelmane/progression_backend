<?php

require_once __DIR__ . '/controleur.php';
require_once 'domaine/entités/question_prog.php';

class ProgCtl extends Controleur
{
    const LANG_NOMS = [
        "Python 2",
        "Python 3",
        "Ruby",
        "",
        "PHP",
        "",
        "",
        "Go",
        "C++",
        "C",
        "Java",
        "Bash",
        "Perl",
        "SSH",
        "MySQL",
    ];

    function __construct($source, $user_id)
    {
        parent::__construct($source, $user_id);

        $this->à_valider =
            isset($_REQUEST["valider"]) &&
            ((isset($_REQUEST["reponse"]) && $_REQUEST["reponse"] != "") ||
                (isset($_REQUEST["incode"]) && $_REQUEST["incode"] != ""));
        $this->à_exécuter =
            isset($_REQUEST["exécuter"]) &&
            isset($_REQUEST["incode"]) &&
            $_REQUEST["incode"] != "";
        $this->reponse =
            isset($_REQUEST["reponse"]) && $_REQUEST["reponse"] != ""
                ? $_REQUEST["reponse"]
                : null;
        $this->reset =
            isset($_REQUEST["reset"]) && $_REQUEST["reset"] != ""
                ? $_REQUEST["reset"]
                : null;
        $this->langid =
            isset($_REQUEST["langid"]) && $_REQUEST["langid"] != ""
                ? $_REQUEST["langid"]
                : null;
        $this->incode =
            isset($_REQUEST["incode"]) && $_REQUEST["incode"] != ""
                ? $_REQUEST["incode"]
                : null;
        $this->params =
            isset($_REQUEST["params"]) && $_REQUEST["params"] != ""
                ? $_REQUEST["params"]
                : null;
        $this->stdin =
            isset($_REQUEST["stdin"]) && $_REQUEST["stdin"] != ""
                ? $_REQUEST["stdin"]
                : null;
    }

    protected function get_mode($langid)
    {
        if ($langid <= QuestionProg::PYTHON3) {
            return "python/python.js";
        } elseif (
            $langid == QuestionProg::CPP ||
            $langid == QuestionProg::JAVA
        ) {
            return "clike/clike.js";
        }
    }

    protected function calculer_sorties($sorties)
    {
        if ($sorties === false) {
            $output = "";
            $erreurs = "Erreur interne. ";
        }

        return [
            "output" => $this->resume($sorties["stdout"], 21),
            "erreurs" => $sorties["stderr"],
        ];
    }

    protected function resume($in, $lignes_max)
    {
        $lignes = explode("\n", $in);
        $nb_lignes = count($lignes);
        if ($nb_lignes <= $lignes_max) {
            return $in;
        } else {
            $av = round(($lignes_max - 1) / 2);
            $ap = floor(($lignes_max - 1) / 2);
            return implode(
                "\n",
                array_merge(
                    array_slice($lignes, 0, $av),
                    ["..."],
                    array_slice($lignes, -$ap)
                )
            );
        }
    }
}
