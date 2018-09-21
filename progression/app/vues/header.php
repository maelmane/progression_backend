<?php

require_once(__DIR__.'/../modele.php');

$themes=get_themes($_SESSION['user_id']);
$user=new User($_SESSION['user_id']);
foreach($themes as $theme){
    if($titre==$theme->titre) $theme->actif="true";
}

$infos=array("titre"=>$titre,
             "username"=>$user->username,
             "themes"=>$themes,
             "est_admin"=>$user->role == User::ROLE_ADMIN,
             "dashboard_actif"=>$titre=="Tableau de bord"?"true":"");

$template=$GLOBALS['mustache']->loadTemplate("header");
echo $template->render($infos);

?>
