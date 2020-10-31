<?php

require_once __DIR__ . '/controleur.php';
require_once 'domaine/interacteurs/connecter_conteneur.php';
require_once 'domaine/interacteurs/obtenir_avancement.php';
require_once 'domaine/interacteurs/obtenir_question_sys.php';
require_once 'domaine/interacteurs/reinitialiser_conteneur.php';
require_once 'domaine/interacteurs/sauvegarder_avancement.php';
require_once 'domaine/interacteurs/sauvegarder_conteneur.php';
require_once 'domaine/interacteurs/traiter_resultats_sys.php';

class QuestionSysCtl extends Controleur
{
    function __construct($source, $user_id, $question_id)
    {
        parent::__construct($source, $user_id);

        $this->_question_id = $question_id;

        $this->question = (new ObtenirQuestionSysInt(
            $this->_source,
            $user_id
        ))->get_question($this->_question_id);

        $this->avancement = (new ObtenirAvancementInt(
            $this->_source,
            $user_id
        ))->get_avancement($this->_question_id, $this->_question_id);
    }

    function get_page_infos()
    {
        $infos = ["template" => "question_sys"];
        $infos = array_merge($infos, $this->récupérer_paramètres());

        $réponse_serveur = ($this->reset
            ? new RéinitialiserConteneurInt($this->_source)
            : new ConnecterConteneurInt($this->_source)
        )->connexion_conteneur($this->question, $this->avancement);

        $infos = array_merge($infos, $réponse_serveur);

        if ($this->à_valider) {
            $infos["résultats"] = (new TraiterRésultatsSysInt(
                $this->_source
            ))->vérifier_réussite($infos);
        }

        if ($infos["résultats"]["réussi"] == "true") {
            $this->avancement->etat = Question::ETAT_REUSSI;
        }

        (new SauvegarderAvancementInt(
            $this->_source,
            $this->_user_id
        ))->sauvegarder($this->avancement);
        (new SauvegarderConteneurInt(
            $this->_source,
            $this->_user_id
        ))->sauvegarder_conteneur(
            $this->avancement,
            $réponse_serveur["cont_id"]
        );

        return $infos;
    }

    function récupérer_paramètres()
    {
        $this->à_valider = isset($_REQUEST["valider"]);
        $this->reponse = isset($_REQUEST["reponse"])
            ? $_REQUEST["reponse"]
            : "";
        $this->reset = isset($_REQUEST["reset"]);

        $infos = [
            "question" => $this->question,
            "réponse" => $this->reponse,
            "avancement" => $this->avancement,
            "nom_serveur" => $GLOBALS['config']['compilebox_hote'], //TODO changer?
            "url_retour" => "index.php?p=serie&ID=" . $this->question->serieID,
            "titre_retour" => "la liste de questions",
        ];

        return $infos;
    }
}
?>
