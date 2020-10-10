<?php

require_once __DIR__.'/prog.php';
require_once 'domaine/interacteurs/obtenir_avancement.php';
require_once 'domaine/interacteurs/obtenir_question_prog.php';
require_once 'domaine/interacteurs/obtenir_serie.php';
require_once 'domaine/interacteurs/sauvegarder_question.php';

class QuestionProgCtl extends ProgCtl{

	function __construct($source, $user_id, $question_id, $réponse_utilisateur){
		parent::__construct($source, $user_id, $réponse_utilisateur);

		$this->_question_id = $question_id;

		$this->question = (new ObtenirQuestionProgInt($this->_source, $user_id))->get_question( $this->_question_id );
		$this->avancement = (new ObtenirAvancementInt($this->_source, $user_id))->get_avancement( $this->_question_id, $this->_question_id );

		$this->série = (new ObtenirSérieInt($source, $user_id))->get_série($this->question->serieID);
	}
	
	function get_page_infos(){		
		$infos=array_merge(parent::get_page_infos(),
						   array("template"=>"question_prog"),
						   $this->récupérer_paramètres());

		if($this->à_valider || $this->à_exécuter){
			$sorties=$this->exécuter_code($infos);
			$infos=array_merge($infos, $this->calculer_sorties($sorties, $infos));

			if($this->à_valider){
				$infos=array_merge($infos, $this->traiter_résultats($sorties, $infos));

				$infos["output"]=$this->resume($infos["output"], 21);
				$infos["solution"]=$this->resume($this->question->solution, 21);
			}
		}

		return $infos;
	}

	private function récupérer_paramètres(){
		eval($this->question->setup);

		$this->question->pre_exec=str_replace("\r","",eval("return " . $this->question->pre_exec . ";"));
		$this->question->pre_code=str_replace("\r","",eval("return " . $this->question->pre_code . ";"));
		$this->question->post_code=str_replace("\r","",eval("return " . $this->question->post_code . ";"));
		$this->question->énoncé=str_replace("\r","",eval("return \"" . $this->question->enonce . "\";"));
		$this->question->solution=str_replace("\r","",eval("return " . $this->question->solution . ";"));

		$infos=array("question"=>$this->question,
					 "titre"=>$this->série->titre,
					 "code"=>$this->get_code($this->question, $this->avancement),
					 "langid"=>$this->question->lang or QuestionProg::PYTHON3,
					 "première_ligne_éditeur_precode"=>$this->compter_lignes($this->question->pre_exec)+1,
					 "première_ligne_éditeur_incode"=>$this->compter_lignes($this->question->pre_exec)+$this->compter_lignes($this->question->pre_code)+1,
					 "params"=>$this->get_params($this->question),
					 //Évalue seulement si stdin provient de la BD
					 "stdin"=>($this->question->stdin==""?$this->get_stdin($this->question):str_replace("\r","",eval("return ".$this->get_stdin($this->question).";"))),
					 "url_retour"=>"index.php?p=serie&ID=".$this->question->serieID,
					 "titre_retour"=>"la liste de questions",
					 "état_réussi"=>$this->avancement->etat==Question::ETAT_REUSSI,
					 "mode"=>$this->get_mode($this->question->lang),
					 "lang_nom"=>ProgCtl::LANG_NOMS[$this->question->lang]
		);

		return $infos;
	}

	private function compter_lignes($texte){
		if($texte==""){
			return 0;
		}
		else{
			return count(preg_split('/\n/',$texte));
		}
	}

	private function traiter_résultats($sorties, $infos){
		$résultats=array();

		$résultats["essayé"]="true";
		if($this->vérifier_solution($sorties, $this->question->solution)){
			$this->sauvegarder_état_réussi($infos['code']);
			$résultats["réussi"]="true";
		}
		else{
			$this->sauvegarder_état_échec($infos['code']);
			$résultats["nonréussi"]="true";
		}

		$résultats["état_réussi"]=$this->avancement->etat==Question::ETAT_REUSSI;

		return $résultats;
	}

	private function vérifier_solution($sorties, $solution){
		$sortie_standard=$this->extraire_sortie_standard($sorties);
		$sortie_erreur=$this->extraire_sortie_erreur($sorties);

		//en PHP, "" == NULL (arg!!!)
		return $solution!="null" && $sortie_standard==$solution;
	}

	private function sauvegarder_état_réussi($code){
		$interacteur = new SauvegarderQuestionInt($this->_source, $this->_user_id);
		$interacteur->set_avancement_réussi($this->question->id, $code );
	}

	private function sauvegarder_état_échec($code){
		$interacteur = new SauvegarderQuestionInt($this->_source, $this->_user_id);
		$interacteur->set_avancement_échec($this->question->id, $code );
	}

}

?>
