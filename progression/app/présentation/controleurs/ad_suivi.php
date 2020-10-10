<?php
require_once 'controleur_admin.php';

class ControleurSuivi extends ControleurAdmin {

	function __construct($id, $user_id){
		parent::__construct($id, $user_id);
	}

	function get_page_infos(){
		$infos=array("template"=>"ad_suivi");
		
		$infos=array_merge($infos, $this->récupérer_paramètres());

		return $infos;
	}

	function récupérer_paramètres(){
		$infos=array();
		
		if(!isset($_GET['u'])){
			$infos["users"]=get_users();
		}
		elseif(!isset($_GET['t'])){
			$infos["user_id"]=$_GET['u'];
			$infos["thèmes"]=get_themes();
			foreach($infos["thèmes"] as $thème){
				$thème->avancement = $thème->get_pourcentage_avancement($_GET["u"]);
			}
		}
		else{
			$thème=new Theme($_GET['t']);
			$infos["thème"]=$thème;
			$infos["séries"]=$thème->get_series();
			foreach($infos["séries"] as $série){
				$série->avancement = $série->get_pourcentage_avancement($_GET["u"]);
			}
		}

		return $infos;
	}

}
?>
