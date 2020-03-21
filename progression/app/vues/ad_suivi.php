<?php

include('admin.php');

function page_contenu(){
	$infos=récupérer_paramètres();
	
    render_page($infos);
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

function render_page($infos){
	$template=$GLOBALS['mustache']->loadTemplate("ad_suivi");
	echo $template->render($infos);
}


?>
