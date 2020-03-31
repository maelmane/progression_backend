<?php

require_once('controleur_prog.php');
require_once('helpers.php');

class ControleurQuestionProg extends ControleurProg{

	function get_page_infos(){
		$infos=array("template"=>"question_prog");
		
		$this->charger_question();
		$this->charger_avancement();

		$infos=array_merge($infos, $this->récupérer_paramètres($this->question, $this->avancement));

		if($this->à_valider || $this->à_exécuter){
			$sorties=$this->exécuter_code($infos);
			$infos=array_merge($infos, $this->calculer_sorties($sorties, $infos));

			if($this->à_valider){
				$infos=array_merge($infos, $this->traiter_résultats($sorties, $infos, $this->avancement, $this->question));

				$infos["output"]=$this->resume($infos["output"], 21);
				$infos["solution"]=$this->resume($this->question->solution, 21);
			}
		}

		$infos["type_prog"]=1;
		return $infos;

	}

	function charger_question(){
		$this->question=new QuestionProg($this->id);
	}

	function charger_avancement(){
		$this->avancement=new Avancement($this->question->id, $this->user_id);
	}

	function récupérer_paramètres($question, $avancement){
		eval($question->setup);

		$question->pre_exec=str_replace("\r","",eval("return $question->pre_exec;"));
		$question->pre_code=str_replace("\r","",eval("return $question->pre_code;"));
		$question->post_code=str_replace("\r","",eval("return $question->post_code;"));
		$question->énoncé=str_replace("\r","",eval("return \"$question->enonce\";"));
		$question->solution=str_replace("\r","",eval("return $question->solution;"));

		$série=new Serie($question->serieID);
		$infos=array("question"=>$question,
					 "titre"=>$série->titre,
					 "code"=>$this->get_code($question, $avancement),
					 "langid"=>$this->langid or $question->lang or QuestionProg::PYTHON3,
					 "première_ligne_éditeur_precode"=>$this->compter_lignes($question->pre_exec)+1,
					 "première_ligne_éditeur_incode"=>$this->compter_lignes($question->pre_exec)+$this->compter_lignes($question->pre_code)+1,
					 "params"=>$this->get_params($question),
					 //Évalue seulement si stdin provient de la BD
					 "stdin"=>($question->stdin==""?$this->get_stdin($question):str_replace("\r","",eval("return ".$this->get_stdin($question).";"))),
					 "url_retour"=>"index.php?p=serie&ID=".$question->serieID,
					 "titre_retour"=>"la liste de questions",
					 "état_réussi"=>$avancement->get_etat()==Question::ETAT_REUSSI,
					 "mode"=>$this->get_mode($question->lang),
					 "lang_nom"=>ControleurProg::LANG_NOMS[$question->lang]
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

	function traiter_résultats($sorties, $infos, $avancement, $question){
		$résultats=array();

		$résultats["essayé"]="true";
		if($this->vérifier_solution($sorties, $this->question->solution)){
			$this->sauvegarder_état_réussi($avancement, $infos['code']);
			$résultats["réussi"]="true";
		}
		else{
			$this->sauvegarder_état_échec($avancement, $infos['code']);
			$résultats["nonréussi"]="true";
		}

		$résultats["état_réussi"]=$avancement->get_etat()==Question::ETAT_REUSSI;

		return $résultats;
	}

	function vérifier_solution($sorties, $solution){
		$sortie_standard=$this->extraire_sortie_standard($sorties);
		$sortie_erreur=$this->extraire_sortie_erreur($sorties);

		//en PHP, "" == NULL (arg!!!)
		return $solution!="null" && $sortie_standard==$solution;
	}

	function sauvegarder_état_réussi($avancement, $code){
		$avancement->set_code($code);
		$avancement->set_etat(Question::ETAT_REUSSI);
	}

	function sauvegarder_état_échec($avancement, $code){
		//Met la réponse à jour dans l'avancement seulement
		//si la question n'avait pas déjà été réussie
		if($avancement->get_etat()!=Question::ETAT_REUSSI){
			$avancement->set_code($code);
			$avancement->set_etat(Question::ETAT_NONREUSSI);
		}
	}

	function sauvegarder_état_non_réussi($avancement, $code){
		$avancement->set_etat(Question::ETAT_NONREUSSI);
	}

}

?>
