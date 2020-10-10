<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';
require_once 'config.php';
require_once 'domaine/entités/user.php';
require_once 'présentation/controleurs/header_footer.php';
require_once 'dao/dao_factory.php';

require_once 'domaine/interacteurs/obtenir_question.php';

require_once 'présentation/controleurs/accueil.php';
require_once 'présentation/controleurs/ad_suivi.php';
require_once 'présentation/controleurs/login.php';
require_once 'présentation/controleurs/logout.php';
require_once 'présentation/controleurs/pratique.php';
require_once 'présentation/controleurs/question_bd.php';
require_once 'présentation/controleurs/question_prog.php';
require_once 'présentation/controleurs/question_sys.php';
require_once 'présentation/controleurs/serie.php';
require_once 'présentation/controleurs/theme.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

set_locale();
openlog("quiz",LOG_NDELAY, LOG_SYSLOG);
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
				$controleur=new LogoutCtl(null, null);
			}
			if($fichier=="theme"){
				$thème_id=$_REQUEST["ID"];
				$controleur=new ThèmeCtl(new DAOFactory(), $user_id, $thème_id );
			}
			elseif($fichier=="serie"){
				$série_id=$_REQUEST["ID"];
				$controleur=new SérieCtl(new DAOFactory(), $série_id, $user_id);
			}
			elseif($fichier=="question"){
				$question_id = $_REQUEST["ID"];
				$question=(new ObtenirQuestionInt( new DAOFactory(), $user_id ))->get_question( $question_id );

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
					$controleur=new QuestionProgCtl(new DAOFactory(), $user_id, $question->id, $réponse_utilisateur);
				}
				elseif($question->type==Question::TYPE_SYS){
					$controleur=new QuestionSysCtl(new DAOFactory(), $user_id, $question->id, $réponse_utilisateur);
				}
				elseif($question->type==Question::TYPE_BD){
					$controleur=new QuestionBdCtl(new DAOFactory(), $user_id, $question->id, $réponse_utilisateur);
				}
			}
			elseif($fichier=="pratique"){
				$réponse_utilisateur=array( "à_exécuter"=>isset($_REQUEST["exécuter"]) && isset($_REQUEST["incode"]) && $_REQUEST["incode"]!="",
											"langid"=>isset($_REQUEST["langid"])&& $_REQUEST["langid"]!="" ?$_REQUEST["langid"]:null,
											"incode"=>isset($_REQUEST["incode"]) &&  $_REQUEST["incode"]!="" ?$_REQUEST["incode"]:null,
											"params"=>isset($_REQUEST["params"]) &&  $_REQUEST["params"]!="" ?$_REQUEST["params"]:null,
											"stdin"=>isset($_REQUEST["stdin"]) &&  $_REQUEST["stdin"]!="" ?$_REQUEST["stdin"]:null);

				$controleur=new PratiqueCtl(new DAOFactory(), $user_id, $réponse_utilisateur);
			}
			elseif($fichier=="ad_suivi"){
				$controleur=new SuiviCtl(new DAOFactory(), $user_id);
			}

			if($controleur==null){
				$controleur=new AccueilCtl(new DAOFactory(), $user_id);
			}
			
		}
		else{
			$controleur=new AccueilCtl(new DAOFactory(), $user_id);
		}        
	}
	else{    
		$réponse_utilisateur=array( "submit"=>isset($_REQUEST["submit"]),
									"username" => (isset($_REQUEST["username"]) ? $_REQUEST["username"] : null),
									"passwd" => (isset($_REQUEST["passwd"]) ? $_REQUEST["passwd"] : null));
		
		$controleur=new LoginCtl(new DAOFactory(), $réponse_utilisateur);

		if ( isset($_REQUEST["submit"] ) ) {
			syslog(LOG_INFO, "Tentative de connexion : " . $_REQUEST["username"]);

			$user = $controleur->effectuer_login( $_REQUEST["username"], isset( $_REQUEST["password"] ) ? $_REQUEST["password"] : null );
			
			if ( isset($user) && $user != null ) {
				syslog(LOG_INFO, "Connexion : " . $user->username);

				$_SESSION['user_id'] = $user->id;
				$controleur = new AccueilCtl(new DAOFactory(), $user->id);
			}

		}
	}
	
	render_page(isset($_SESSION['user_id']) ? $user_id : null, isset($thèmeID) ? $thèmeID : null, $controleur);

}

function render_page($user_id, $thèmeID, $controleur){
	$infos = array();
	
	if ( ! is_null($user_id) ){
		$infos=array_merge((new HeaderFooterCtl(new DAOFactory(), $user_id))->get_header_infos($thèmeID, $user_id));
	}

	syslog(LOG_INFO, "Controleur : " . get_class($controleur));
	$infos=array_merge($infos, $controleur->get_page_infos());
	
	$template=$GLOBALS['mustache']->loadTemplate($infos["template"]);
	echo $template->render($infos);
}

?>
