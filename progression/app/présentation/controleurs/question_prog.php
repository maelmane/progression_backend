<?php

require_once('controleur_prog.php');
require_once('domaine/interacteurs/question_prog_interacteur.php');
require_once('helpers.php');

class ControleurQuestionProg extends ControleurProg{

	function __construct($source, $user_id, $question_id, $réponse_utilisateur){
		parent::__construct($source, $user_id, $réponse_utilisateur);

		$this->_question_id = $question_id;

		$interacteur = new QuestionProgInteracteur($this->_source, $user_id);
		$this->question = $interacteur->get_question( $this->_question_id );
		
		$this->avancement = $interacteur->get_avancement( $this->_question_id, $this->_question_id );

		$interacteur = new SérieInteracteur($source, $user_id);
		$this->série = $interacteur->get_série($this->question->serieID);
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

	function récupérer_paramètres(){
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
					 "lang_nom"=>ControleurProg::LANG_NOMS[$this->question->lang]
		);

		return $infos;
	}

	function compter_lignes($texte){
		if($texte==""){
			return 0;
		}
		else{
			return count(preg_split('/\n/',$texte));
		}
	}

	function traiter_résultats($sorties, $infos){
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

	function vérifier_solution($sorties, $solution){
		$sortie_standard=$this->extraire_sortie_standard($sorties);
		$sortie_erreur=$this->extraire_sortie_erreur($sorties);

		//en PHP, "" == NULL (arg!!!)
		return $solution!="null" && $sortie_standard==$solution;
	}

	function sauvegarder_état_réussi($code){
		$interacteur = new QuestionInteracteur($this->_source, $this->_user_id);
		$interacteur->set_avancement_réussi($this->question->id, $code );
	}

	function sauvegarder_état_échec($code){
		$interacteur = new QuestionInteracteur($this->_source, $this->_user_id);
		$interacteur->set_avancement_échec($this->question->id, $code );
	}

}

?>
