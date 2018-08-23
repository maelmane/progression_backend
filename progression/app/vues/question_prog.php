<?php

require_once('quiz_preambule.php');
require_once('prog.php');

$question=charger_question_ou_terminer();
$avancement=charger_avancement();

//infos contient tout ce qui a été envoyé pour compilation/exécution
$infos=évaluer_composantes($question, $avancement);

page_header();
prog_header($infos['langid']); //Attention! deuxième section <head>
prog_contenu($infos, $avancement, $question);
prog_footer($infos);
page_footer();

function charger_question_ou_terminer(){
    $question=new QuestionProg($_GET['ID']);

    if(is_null($question->id)){
        header('Location: index.php?p=accueil');
    }

    return $question;
}

function charger_avancement(){
    $avancement=new Avancement($_GET['ID'], $_SESSION['user_id']);

    return $avancement;
}

function évaluer_composantes($question, $avancement){
    require_once('helpers.php');
    
    eval($question->setup);
    $infos=array("langid"=>get_langage(),
                 "code"=>get_code($question, $avancement),
                 "params"=>get_params($question),
                 //Évalue seulement si stdin provient de la BD
                 "stdin"=>($question->stdin==""?get_stdin($question):str_replace("\r","",eval("return ".get_stdin($question).";"))),
                 "enonce"=>str_replace("\r","",eval("return \"$question->enonce\";")),
                 "pre_exec"=>str_replace("\r","",eval("return $question->pre_exec;")),
                 "pre_code"=>str_replace("\r","",eval("return $question->pre_code;")),
                 "post_code"=>str_replace("\r","",eval("return $question->post_code;")),
                 "reponse"=>str_replace("\r","",eval("return $question->reponse;")),
                 "lang_id"=>$question->lang);
    
    return $infos;
}

function prog_contenu($infos, $avancement, $question){
    afficher_intro($question->titre, $infos['enonce']);
    afficher_formulaires($infos);
    $sorties=exécuter_code($infos);
    afficher_sorties($sorties);
    $sortie_standard=extraire_sortie_standard($sorties);
    afficher_résultats($sortie_standard, $infos, $avancement, $question); 
}

function afficher_formulaires($infos){
    afficher_formulaire_header();
    afficher_formulaire_éditeur_precode($infos);
    afficher_formulaire_éditeur_code($infos);
    afficher_formulaire_éditeur_postcode($infos);
    afficher_formulaire_champs_params_stdin($infos);
    afficher_formulaire_footer();
}

?>
