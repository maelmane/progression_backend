<?php

require_once(__DIR__.'/../../domaine/entités/user.php');
require_once(__DIR__.'/../../domaine/interacteurs/themes_interacteur.php');

function get_header_infos($source_themes, $thèmeID, $user_id){
	$user=new User($user_id);
	$themes=ThemeInteracteur::get_themes($source_themes, $user);
	$themes[$thèmeID]->courant="true";

	$infos=array("username"=>$user->username,
				 "themes"=>$themes,
				 "est_admin"=>$user->role == User::ROLE_ADMIN,
				 "dashboard_actif"=>$themes[0]->actif);

	return $infos;
}

function get_footer_infos(){
	return array();
}

?>
