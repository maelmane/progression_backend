<?php

require_once __DIR__ . '/prog.php';
require_once 'domaine/interacteurs/executer_prog.php';

class PratiqueCtl extends ProgCtl
{
    function get_page_infos()
    {
        $infos = array_merge(
            parent::get_page_infos(),
            $this->récupérer_paramètres()
        );

        if ($this->à_valider) {
            $sorties = (new ExécuterProgInt(
                $this->_source,
                $this->_user_id
            ))->exécuter($infos["exécutable"]);

            $infos = array_merge(
                $infos,
                ["résultats" => ["essayé" => "true"]],
                $this->calculer_sorties($sorties)
            );
        }
        return $infos;
    }

    protected function get_code()
    {
        return isset($_REQUEST["incode"]) ? $_REQUEST["incode"] : "";
    }

    protected function get_params()
    {
        return isset($_REQUEST["params"]) ? $_REQUEST["params"] : "";
    }

    protected function get_stdin()
    {
        return isset($_REQUEST["stdin"]) ? $_REQUEST["stdin"] : "";
    }

    protected function get_langid()
    {
        return isset($_REQUEST["langid"]) ? $_REQUEST["langid"] : "";
    }

    private function récupérer_paramètres()
    {
        $exécutable = new class {};

        $exécutable->langid = $this->get_langid();
        $exécutable->pre_exec = "";
        $exécutable->pre_code = "";
        $exécutable->code = $this->get_code();
        $exécutable->post_code = "";
        $exécutable->params = $this->get_params();
        $exécutable->stdin = $this->get_stdin();

        $infos = [
            "template" => "pratique",
            "question.titre" => "Zone de pratique libre",
            "question.enonce" => "Cette zone permet d'exécuter vos programmes dans le langage de votre choix. 
                            <br>
                            <em>Attention, vos programmes entrés ici ne seront pas sauvegardés.</em>",
            "url_retour" => "index.php?p=accueil",
            "titre_retour" => "l'accueil",
            "mode" => $this->get_mode($this->langid),
            "exécutable" => $exécutable,
        ];

        return $infos;
    }
}
