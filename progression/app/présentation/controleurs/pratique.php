<?php

require_once __DIR__.'/prog.php';

class PratiqueCtl extends ProgCtl {

	function __construct($source, $user_id, $réponse_utilisateur){
		parent::__construct($source, $user_id, $réponse_utilisateur);
	}
	
	function get_page_infos(){
		$infos=array_merge(
			parent::get_page_infos(),
			$this->récupérer_paramètres());
		
		$sorties=$this->exécuter_code($infos);
		$infos=array_merge(
			$infos,
			$this->calculer_sorties($sorties, $infos));
		
		return $infos;
	}

	private function récupérer_paramètres(){
		$infos= array("template"=>"pratique",
					  "question.titre"=>"Zone de pratique libre",
					  "question.enonce"=>"Cette zone permet d'exécuter vos programmes dans le langage de votre choix. 
                            <br>
                            <em>Attention, vos programmes entrés ici ne seront pas sauvegardés.</em>",
					  "langid"=>$this->langid,
					  "code"=>$this->get_code(),
					  "params"=>$this->get_params(),
					  "stdin"=>$this->get_stdin(),
					  "url_retour"=>"index.php?p=accueil",
					  "titre_retour"=>"l'accueil",
					  "mode"=>$this->get_mode($this->langid)
		);
		
		return $infos;
	}

}
