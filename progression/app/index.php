<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/modele.php');
require_once(__DIR__.'/controleurs/header_footer.php');

require("controleurs/accueil.php");
require("controleurs/ad_suivi.php");
require("controleurs/pratique.php");
require("controleurs/serie.php");
require("controleurs/question_prog.php");
require("controleurs/question_sys.php");
require("controleurs/question_bd.php");
require("controleurs/theme.php");

vérifier_user_id();
set_locale();
openlog("quiz",LOG_NDELAY, LOG_LOCAL0);
inclusion_page();

function vérifier_user_id(){
    if(!isset($_SESSION["user_id"])){
        header("Location: /login.php".(isset($_GET[p])?"?p=".$_GET["p"]."&ID=".$_GET["ID"]:"").(isset($_GET['ID'])?("&ID=".$_GET['ID']):""));
    }
}

function set_locale(){
    $locale=isset($GLOBALS['config']['locale'])?$GLOBALS['config']['locale']:'fr_CA.UTF-8';
    setlocale(LC_ALL,$locale);
}

function inclusion_page(){
    if(isset($_SESSION["user_id"])){
        if(isset($_GET["p"])){
            $fichier=$_GET["p"];

			$controleur=null;

			$thèmeID=0;
            if($fichier=="theme"){
				$thèmeID=$_REQUEST["ID"];
				$controleur=new ControleurThème($_REQUEST["ID"], $_SESSION["user_id"]);
			}
            elseif($fichier=="serie"){
				$série=new Serie($_REQUEST["ID"]);
				$thèmeID=$série->themeID;
				$controleur=new ControleurSérie($_REQUEST["ID"], $_SESSION["user_id"]);
			}
			elseif($fichier=="question"){
				$question=new Question($_REQUEST["ID"]);
				$série=new Serie($question->serieID);
				$thèmeID=$série->themeID;

				$réponse_utilisateur=array( "à_exécuter"=>isset($_REQUEST["exécuter"]) && isset($_REQUEST["incode"]) && $_REQUEST["incode"]!="",
											"à_valider"=>(isset($_REQUEST["valider"]) &&
														  (isset($_REQUEST["reponse"]) && $_REQUEST["reponse"]!="" || isset($_REQUEST["incode"]) && $_REQUEST["incode"]!="")),
											"langid"=>isset($_REQUEST["langid"])&& $_REQUEST["langid"]!="" ?$_REQUEST["langid"]:null,
											"reponse"=>isset($_REQUEST["reponse"]) &&  $_REQUEST["reponse"]!="" ?$_REQUEST["reponse"]:null,
											"reset"=>isset($_REQUEST["reset"]) &&  $_REQUEST["reset"]!="" ?$_REQUEST["reset"]:null,
											"incode"=>isset($_REQUEST["incode"]) &&  $_REQUEST["incode"]!="" ?$_REQUEST["incode"]:null,
											"params"=>isset($_REQUEST["params"]) &&  $_REQUEST["params"]!="" ?$_REQUEST["params"]:null,
											"stdin"=>isset($_REQUEST["stdin"]) &&  $_REQUEST["stdin"]!="" ?$_REQUEST["stdin"]:null);
				
				if($question->type==Question::TYPE_PROG){
					$controleur=new ControleurQuestionProg($_REQUEST["ID"], $_SESSION["user_id"], $réponse_utilisateur);
				}
				elseif($question->type==Question::TYPE_SYS){
					$controleur=new ControleurQuestionSys($_REQUEST["ID"], $_SESSION["user_id"], $réponse_utilisateur);
				}
				elseif($question->type==Question::TYPE_BD){
					$controleur=new ControleurQuestionBd($_REQUEST["ID"], $_SESSION["user_id"], $réponse_utilisateur);
				}
			}
			elseif($fichier=="pratique"){
				$réponse_utilisateur=array( "à_exécuter"=>isset($_REQUEST["exécuter"]) && isset($_REQUEST["incode"]) && $_REQUEST["incode"]!="",
											"langid"=>isset($_REQUEST["langid"])&& $_REQUEST["langid"]!="" ?$_REQUEST["langid"]:null,
											"incode"=>isset($_REQUEST["incode"]) &&  $_REQUEST["incode"]!="" ?$_REQUEST["incode"]:null,
											"params"=>isset($_REQUEST["params"]) &&  $_REQUEST["params"]!="" ?$_REQUEST["params"]:null,
											"stdin"=>isset($_REQUEST["stdin"]) &&  $_REQUEST["stdin"]!="" ?$_REQUEST["stdin"]:null);

				$controleur=new ControleurPratique(null, $_SESSION["user_id"], $réponse_utilisateur);
			}
			elseif($fichier=="ad_suivi"){
				$controleur=new ControleurSuivi(null, $_SESSION["user_id"]);
			}
			if($controleur==null){
				$controleur=new ControleurAccueil(null, $_SESSION["user_id"]);
			}
			
			render_page($thèmeID, $controleur);
		}
		else{
			header("Location: index.php?p=accueil");
		}        
	}
	else{    
		if(isset($_GET["p"])){
			header("Location: /login.php?p=$_GET[p]" . (isset($_GET['ID'])?("&ID=".$_GET['ID']):""));
		}
		else{
			header("Location: /login.php");
		}
	}
}

function render_page($thèmeID, $controleur){
	$infos=get_header_infos($thèmeID, $_SESSION["user_id"]);
	$infos=array_merge($infos, $controleur->get_page_infos());
	$infos=array_merge($infos, get_footer_infos());			
	
	$template=$GLOBALS['mustache']->loadTemplate($infos["template"]);
	echo $template->render($infos);
}

?>
