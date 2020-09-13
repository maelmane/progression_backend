<?php

require_once('controleur_prog.php');

class ControleurPratique extends ControleurProg {

	function get_page_infos(){
		$infos=$this->récupérer_paramètres();
		$sorties=$this->exécuter_code($infos);
		$infos=array_merge($infos, $this->calculer_sorties($sorties, $infos));
		return $infos;
	}

	function récupérer_paramètres(){
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
					  "mode"=>$this->get_mode($langid)
		);
		return $infos;
	}

}
