<?php

$themes = get_themes($_SESSION['user_id']);
$username = $_SESSION['nom'];
$est_admin = $username=="admin"?"true":"";
$dashboard_actif = $titre=="Tableau de bord"?"true":"";
foreach($themes as $theme){
    if($titre==$theme->titre) $theme->actif="true";
}

$infos=array("titre"=>$titre,
             "username"=>$username,
             "themes"=>$themes,
             "est_admin"=>$est_admin,
             "dashboard_actif"=>$dashboard_actif);

$template=$GLOBALS['mustache']->loadTemplate("header");
echo $template->render($infos);

?>
