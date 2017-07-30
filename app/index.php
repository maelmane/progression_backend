<?php

$whitelist='/accueil|theme|serie|question|pratique|systeme/';

if(!isset($_GET["p"]))
    require("login.php");
else{
    $fichier=$_GET["p"];
    if(preg_match($whitelist, $fichier) && file_exists("vues/$fichier.php"))
        require("vues/$fichier.php");
    else
        header("Location: index.php");
}
?>