<?php

require_once __DIR__.'/controleur.php';
require_once 'domaine/entités/question_prog.php';

class ProgCtl extends Controleur {

	const LANG_NOMS=array("Python 2",
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
						  "MySQL");
	
	function __construct($source, $user_id, $réponse_utilisateur){
		parent::__construct($source, $user_id);

		$this->à_valider=isset($réponse_utilisateur["à_valider"])?$réponse_utilisateur["à_valider"]:null;
		$this->à_exécuter=isset($réponse_utilisateur["à_exécuter"])?$réponse_utilisateur["à_exécuter"]:null;
		$this->reponse=isset($réponse_utilisateur["reponse"])?$réponse_utilisateur["reponse"]:null;
		$this->reset=isset($réponse_utilisateur["reset"])?$réponse_utilisateur["reset"]:null;
		$this->langid=isset($réponse_utilisateur["langid"])?$réponse_utilisateur["langid"]:null;
		$this->incode=isset($réponse_utilisateur["incode"])?$réponse_utilisateur["incode"]:null;
		$this->params=isset($réponse_utilisateur["params"])?$réponse_utilisateur["params"]:null;
		$this->stdin=isset($réponse_utilisateur["stdin"])?$réponse_utilisateur["stdin"]:null;
	}

	protected function get_code(){
		$code="";
		
		if ($this->incode!=null){
			$code=$this->incode;
		}
		else{
			if(!is_null($this->avancement) && $this->avancement->code!=''){
				$code=$this->avancement->code;
			}
			elseif(!is_null($this->question)){
				$code=$this->question->incode;
			}
		}
		
		return $code;
	}

	protected function get_params(){
		$params="";
		
		if(!is_null($this->question) && $this->question->params!=""){
			$params=$this->question->params;
		}
		elseif($this->params!=null){
			$params=$this->params;
		}

		return $params;
	}

	protected function get_stdin(){
		$stdin="";
		if(!is_null($this->question) && $this->question->stdin!=""){
			$stdin=$this->question->stdin;
		}
		elseif($this->stdin!=null){
			$stdin=$this->stdin;
		}

		return $stdin;
	}

	protected function get_mode($langid){
		if($langid<=QuestionProg::PYTHON3){
			return "python/python.js";
		}
		elseif($langid==QuestionProg::CPP || $langid==QuestionProg::JAVA){
			return "clike/clike.js";
		}
	}

	protected function exécuter_code($infos){
		ProgCtl::loguer_code($infos);

		//Extrait les infos
		$langid=$infos["langid"];
		$pre_exec=$infos["question"]->pre_exec;
		$pre_code=$infos["question"]->pre_code;
		$code=$infos["code"];
		$post_code=$infos["question"]->post_code; 
		$params=$infos["params"];
		$stdin=$infos["stdin"];

		//Compose le code à exécuter
		$code_exec=preg_replace('~\R~u', "\n", $pre_exec. $pre_code . "\n" . $code . "\n" . $post_code);

		//post le code à remotecompiler
		$url_rc='http://' . $GLOBALS['config']['compilebox_hote'] . ':' . $GLOBALS['config']['compilebox_port'] .'/compile'; //TODO à changer ?
		$data_rc=array('language' => $langid, 'code' => $code_exec, 'parameters' => "\"$params\"", 'stdin' => $stdin, 'vm_name' => 'remotecompiler');
		$options_rc=array('http'=> array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data_rc)));
		$context=stream_context_create($options_rc);
		$comp_resp=file_get_contents($url_rc, false, $context);

		return $comp_resp;
	}

	protected function calculer_sorties($sorties, $infos){
		if ($sorties === FALSE) {
			$output="";
			$erreurs="Erreur interne. ";        
		}
		else{
			$output=ProgCtl::extraire_sortie_standard($sorties);
			$erreurs=ProgCtl::extraire_sortie_erreur($sorties);
		}

		return array("output"=>$output,
					 "erreurs"=>$erreurs);
	}

	protected function loguer_code($infos){
		$com_log=$_SERVER['REMOTE_ADDR']." - " . $_SERVER["PHP_SELF"] . " : lang : " . $infos['langid'] . " Code : ". $infos['code']; //TODO à changer ?
		syslog(LOG_INFO, $com_log);
	}

	protected function extraire_sortie_standard($sorties){
		return str_replace("\r","",json_decode($sorties, true)['output']);
	}

	protected function extraire_sortie_erreur($sorties){
		return json_decode($sorties, true)['errors'];
	}

	protected function resume($in, $lignes_max){
		$lignes=explode("\n", $in);
		$nb_lignes=count($lignes);
		if ($nb_lignes<=$lignes_max){
			return $in;
		}
		else{
			$av=round(($lignes_max-1)/2);
			$ap=floor(($lignes_max-1)/2);
			return implode("\n", array_merge(array_slice($lignes,0,$av),array("..."),array_slice($lignes,-$ap)));
		} 
	}

}
