<?php

require_once('quiz_preambule.php');
require_once('prog.php');

$infos=array("langid"=>get_lang_defaut(),
             "code"=>get_code(),
             "params"=>get_params(),
             "stdin"=>get_stdin());

page_header();
prog_header($infos['langid']); //Attention! deuxième section <head>
page_contenu($infos);
prog_footer();
page_footer();

function page_contenu($infos){
    $titre="Zone de pratique libre";
    $énoncé="Cette zone permet d'exécuter vos programmes dans le langage de votre choix. 
             <br>
             <em>Attention, vos programmes entrés ici ne seront pas sauvegardés.</em>";
    afficher_intro($titre, $énoncé);
    afficher_formulaire($infos);
    $output=exécuter($infos);
    afficher_sorties($output);
}

function afficher_formulaire($infos){
    afficher_formulaire_header();
    afficher_formulaire_selection_langage($infos);
    afficher_formulaire_code($infos);
    afficher_formulaire_params_stdin($infos);
    afficher_retour("index.php?p=accueil", "l'accueil");
    afficher_formulaire_footer();
}
