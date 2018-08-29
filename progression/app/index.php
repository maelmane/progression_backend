<?php

session_start();
require __DIR__ . '/vendor/autoload.php';
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/modele.php');

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

function page_header($titre=null){
    if(is_null($titre))
        $titre = "";
    include 'vues/header.php';
}

function inclusion_page(){
    if(isset($_SESSION["user_id"])){
        if(isset($_GET["p"])){
            $fichier=$_GET["p"];
            if(est_admissible($fichier))
                require("vues/$fichier.php");
            else
                require("vues/accueil.php");
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

function est_admissible($fichier){
    #Hardcodé, à mettre dans un fichier de config.
    $whitelist='/accueil|theme|serie|question|pratique|systeme|admin|ad_ajout_t|ad_ajout_s|ad_ajout_q|ad_suivi/';

    return preg_match($whitelist, $fichier) && file_exists("vues/$fichier.php");
}
?>
