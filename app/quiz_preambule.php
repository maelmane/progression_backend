<?php

require_once('modele.php');
vérifier_paramètres();
set_locale();
openlog("quiz",LOG_NDELAY, LOG_LOCAL0);

function vérifier_paramètres(){
    if(!isset($_GET['ID'])){
        header('Location: index.php?p=accueil');
    }
    elseif(!isset($_SESSION["user_id"])){
        header("Location: login.php?p=$_GET[p]&ID=$_GET[ID]");
    }
}

function set_locale(){
    $locale=isset($GLOBALS['config']['locale'])?$GLOBALS['config']['locale']:'fr_CA.UTF-8';
    setlocale(LC_ALL,$locale);
}

function page_header($titre=null){
    if(is_null($titre))
        $titre = "";
    include 'templates/header.php';
}

function page_footer(){
    include 'templates/footer.php';
}


?>
