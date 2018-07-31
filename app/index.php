<?php

$whitelist='/accueil|theme|serie|question|pratique|systeme|admin|ad_ajout_t|ad_ajout_s|ad_ajout_q|ad_suivi/';

session_start();

if(!isset($_SESSION["user_id"])){
    if(!isset($_GET["p"])){
        header("Location: login.php");
    }
    else{
        header("Location: login.php?p=$_GET[p]" . (isset($_GET['ID'])?("&ID=".$_GET['ID']):""));
    }
}
else{
    if(!isset($_GET["p"])){
        header("Location: login.php?p=accueil");
    }
    else{
        $fichier=$_GET["p"];
        if(preg_match($whitelist, $fichier) && file_exists("vues/$fichier.php"))
            require("vues/$fichier.php");
        else
            require("vues/accueil.php");
    }
}
?>
