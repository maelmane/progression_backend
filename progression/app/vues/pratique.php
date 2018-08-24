<?php

require_once('quiz_preambule.php');
require_once('prog.php');

$infos=array("langid"=>get_langage(),
             "code"=>get_code(),
             "params"=>get_params(),
             "stdin"=>get_stdin());

page_header();
prog_header($infos['langid']); //Attention! deuxième section <head>
page_contenu($infos);
prog_footer($infos);
page_footer();

function page_contenu($infos){
    $titre="Zone de pratique libre";
    $énoncé="Cette zone permet d'exécuter vos programmes dans le langage de votre choix. 
             <br>
             <em>Attention, vos programmes entrés ici ne seront pas sauvegardés.</em>";
    afficher_intro($titre, $énoncé);
    afficher_formulaire($infos);
    $output=exécuter_code($infos);
    afficher_sorties($output);
    afficher_extro();
}

function afficher_formulaire($infos){
    afficher_formulaire_header();
    afficher_formulaire_selection_langage($infos);
    afficher_formulaire_code($infos);
    afficher_formulaire_params_stdin($infos);
    afficher_formulaire_footer();
}

function afficher_extro(){
    afficher_résultats_header();
    $url_retour="index.php?p=accueil";
    $titre_retour="l'accueil";
    afficher_retour_arrière($url_retour, $titre_retour);
    afficher_résultats_footer();
}
