<?php

require_once('quiz_preambule.php');
require_once('prog.php');

page_header();
page_contenu();

function page_contenu(){
    $infos=récupérer_paramètres();
    $sorties=exécuter_code($infos);
    $infos=array_merge($infos, calculer_sorties($sorties, $infos));
    render_page($infos);
}

function récupérer_paramètres(){
    $langid=get_langage();
    $infos= array("titre"=>"Zone de pratique libre",
                  "énoncé"=>"Cette zone permet d'exécuter vos programmes dans le langage de votre choix. 
                            <br>
                            <em>Attention, vos programmes entrés ici ne seront pas sauvegardés.</em>",
                  "langid"=>$langid,
                  "code"=>get_code(),
                  "params"=>get_params(),
                  "stdin"=>get_stdin(),
                  "url_retour"=>"index.php?p=accueil",
                  "titre_retour"=>"l'accueil",
                  "mode"=>get_mode($langid)
    );
    return $infos;
}

function get_mode($langid){
    if($langid<=QuestionProg::PYTHON3){
        return "python/python.js";
    }
    elseif($langid==QuestionProg::CPP || $langid==QuestionProg::JAVA){
        return "clike/clike.js";
    }
}

function calculer_sorties($sorties, $infos){
    if ($sorties === FALSE) {
        $output="Erreur interne. ";
    }
    else{
        $output=extraire_sortie_standard($sorties);
        $erreurs=extraire_sortie_erreur($sorties);
    }

    return array("output"=>resume($output,21),
                 "erreurs"=>$erreurs);
}

function render_page($infos){
    $template=$GLOBALS['mustache']->loadTemplate("pratique");
    echo $template->render($infos);
}

