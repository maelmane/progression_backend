<?php

require_once __DIR__ . '/prog.php';
require_once 'domaine/interacteurs/obtenir_avancement.php';
require_once 'domaine/interacteurs/obtenir_serie.php';
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

		$this->avancement = (new ObtenirAvancementInt(
			$this->_source,
			$user_id
		))->get_avancement($this->_question_id, $this->_question_id);

		$this->série = (new ObtenirSérieInt($source, $user_id))->get_série(
			$this->question->serieID
		);
	}

    protected function get_question(){
		return (new ObtenirQuestionProgInt(
			$this->_source,
			$this->_user_id
		))->get_question($this->_question_id);
    }
    
	public function get_page_infos()
	{
		$infos = array_merge(
			parent::get_page_infos(),
			$this->récupérer_paramètres()
		);

		if ($this->à_valider) {
            $toutes_sorties=array();
            
            foreach ( $infos["exécutables"] as $exécutable ){
                $sorties = (new ExécuterProgInt(
                    $this->_source,
                    $this->_user_id
                ))->exécuter($exécutable);

                $toutes_sorties[] = $this->calculer_sorties($sorties);
            }

            $infos = array_merge($infos, ["sorties" => $toutes_sorties]);
            
            $résultats = (new TraiterRésultatsProgInt(
                $this->_source,
                $this->_user_id
            ))->traiter_résultats(
                $sorties,
                $this->question,
                $exécutable->code
            );

            $infos["résultats"] = $résultats;
		}
		$infos["solution"] = $this->resume($this->question->solution, 21);

		return $infos;
	}

	protected function récupérer_paramètres()
	{
		$infos = [
			"template" => "question_prog",
			"question" => $this->question,
			"titre" => $this->série->titre,
			"url_retour" => "index.php?p=serie&ID=" . $this->question->serieID,
			"titre_retour" => "la liste de questions",
			"état_réussi" => $this->avancement->etat == Question::ETAT_REUSSI,
			"mode" => $this->get_mode($this->question->lang),
			"lang_nom" => ProgCtl::LANG_NOMS[$this->question->lang],
			"exécutables" => $this->get_exécutables(),
		];

		return $infos;
	}

    protected function get_exécutables(){
    }
}

?>
