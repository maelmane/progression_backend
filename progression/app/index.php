<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/domaine/entités/user.php');
require_once(__DIR__.'/présentation/controleurs/header_footer.php');

require_once(__DIR__.'/dao/dao_factory.php');

require("présentation/controleurs/accueil.php");
require("présentation/controleurs/ad_suivi.php");
require("présentation/controleurs/login.php");
require("présentation/controleurs/logout.php");
require("présentation/controleurs/pratique.php");
require("présentation/controleurs/question_bd.php");
require("présentation/controleurs/question_prog.php");
require("présentation/controleurs/question_sys.php");
require("présentation/controleurs/serie.php");
require("présentation/controleurs/theme.php");

//vérifier_user_id();
set_locale();
openlog("quiz",LOG_NDELAY, LOG_LOCAL0);
inclusion_page();

function set_locale(){
    $locale=isset($GLOBALS['config']['locale'])?$GLOBALS['config']['locale']:'fr_CA.UTF-8';
    setlocale(LC_ALL,$locale);
}

function inclusion_page(){
    if(isset($_SESSION["user_id"])){
		$user_id = $_SESSION['user_id'];
        if(isset($_GET["p"])){
            $fichier=$_GET["p"];

			$controleur=null;

			$thèmeID=0;
            if($fichier=="logout"){
				$controleur=new ControleurLogout();
			}
            if($fichier=="theme"){
				$thèmeID=$_REQUEST["ID"];
				$controleur=new ControleurThème($_REQUEST["ID"], $user_id);
			}
            elseif($fichier=="serie"){
				$série=new Serie($_REQUEST["ID"]);
				$thèmeID=$série->themeID;
				$controleur=new ControleurSérie($_REQUEST["ID"], $user_id);
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
					$controleur=new ControleurQuestionProg($_REQUEST["ID"], $user_id, $réponse_utilisateur);
				}
				elseif($question->type==Question::TYPE_SYS){
					$controleur=new ControleurQuestionSys($_REQUEST["ID"], $user_id, $réponse_utilisateur);
				}
				elseif($question->type==Question::TYPE_BD){
					$controleur=new ControleurQuestionBd($_REQUEST["ID"], $user_id, $réponse_utilisateur);
				}
			}
			elseif($fichier=="pratique"){
				$réponse_utilisateur=array( "à_exécuter"=>isset($_REQUEST["exécuter"]) && isset($_REQUEST["incode"]) && $_REQUEST["incode"]!="",
											"langid"=>isset($_REQUEST["langid"])&& $_REQUEST["langid"]!="" ?$_REQUEST["langid"]:null,
											"incode"=>isset($_REQUEST["incode"]) &&  $_REQUEST["incode"]!="" ?$_REQUEST["incode"]:null,
											"params"=>isset($_REQUEST["params"]) &&  $_REQUEST["params"]!="" ?$_REQUEST["params"]:null,
											"stdin"=>isset($_REQUEST["stdin"]) &&  $_REQUEST["stdin"]!="" ?$_REQUEST["stdin"]:null);

				$controleur=new ControleurPratique(null, $user_id, $réponse_utilisateur);
			}
			elseif($fichier=="ad_suivi"){
				$controleur=new ControleurSuivi(null, $user_id);
			}

			if($controleur==null){
				$controleur=new ControleurAccueil(new DAOFactory(), $user_id);
			}
			
		}
		else{
			$controleur=new ControleurAccueil(new DAOFactory(), $user_id);
		}        
	}
	else{    
		$réponse_utilisateur=array( "submit"=>isset($_REQUEST["submit"]),
									"username" => (isset($_REQUEST["username"]) ? $_REQUEST["username"]!="" : null),
									"passwd" => (isset($_REQUEST["passwd"]) ? $_REQUEST["passwd"]!="" : null));
		
		$controleur=new ControleurLogin(new DAOFactory(), $réponse_utilisateur);

		// if(isset($_GET["p"])){
		// 	header("Location: /login.php?p=$_GET[p]" . (isset($_GET['ID'])?("&ID=".$_GET['ID']):""));
		// }
		// else{
		// 	header("Location: /login.php");
		// }
	}

	render_page(isset($_SESSION['user_id']) ? $user_id : null, isset($thèmeID) ? $thèmeID : null, $controleur);

}

function render_page($user_id, $thèmeID, $controleur){
	$infos = array();
	
	if ( ! is_null($user_id) ){
		$infos=array_merge((new HeaderControleur(new DAOFactory(), $user_id))->get_header_infos($thèmeID, $user_id));
	}
	
	$infos=array_merge($infos, $controleur->get_page_infos());
	
	$template=$GLOBALS['mustache']->loadTemplate($infos["template"]);
	echo $template->render($infos);
}

?>
