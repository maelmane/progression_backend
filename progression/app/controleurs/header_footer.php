<?php

require_once(__DIR__.'/../modele.php');

function get_header_infos($thèmeID, $user_id){
	$user=new User($user_id);
	$themes=get_themes($user->role == User::ROLE_ADMIN);
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
