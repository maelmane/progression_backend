<?php

$whitelist='/accueil|theme|serie|question|pratique|systeme|admin/';

session_start();

if(!isset($_SESSION["user_id"])){
    if(!isset($_GET["p"])){
        header("Location: login.php");
    }
    else{
        header("Location: login.php?p=$_GET[p]&ID=$_GET[ID]");
    }
}
else{
    if(!isset($_GET["p"])){
        require("vues/accueil.php");
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