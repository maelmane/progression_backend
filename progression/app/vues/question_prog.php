<?php

require_once('quiz_preambule.php');
require_once('prog.php');

page_header();
page_contenu();

function page_contenu(){
    $question=charger_question_ou_terminer();
    $avancement=charger_avancement();

    $infos=récupérer_paramètres($question, $avancement);
    $sorties=exécuter_code($infos);
    $infos=array_merge($infos, calculer_sorties($sorties, $infos));
    $infos=array_merge($infos, traiter_résultats(extraire_sortie_standard($sorties), $infos, $avancement, $question));
    render_page($infos);
    
}

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

function récupérer_paramètres($question, $avancement){
    require_once('helpers.php');

    $langid=$question->lang;

    eval($question->setup);
    
    $pre_exec=str_replace("\r","",eval("return $question->pre_exec;"));
    $pre_code=str_replace("\r","",eval("return $question->pre_code;"));
    $post_code=str_replace("\r","",eval("return $question->post_code;"));

    $infos=array("titre"=>$question->titre,
                 "langid"=>$langid,
                 "code"=>get_code($question, $avancement),
                 "énoncé"=>str_replace("\r","",eval("return \"$question->enonce\";")),
                 "pre_exec"=>$pre_exec,
                 "pre_code"=>$pre_code,
                 "post_code"=>$post_code,
                 "première_ligne_éditeur_precode"=>compter_lignes($pre_exec)+1,
                 "première_ligne_éditeur_incode"=>compter_lignes($pre_exec)+compter_lignes($pre_code)+1,
                 "params"=>get_params($question),
                 //Évalue seulement si stdin provient de la BD
                 "stdin"=>($question->stdin==""?get_stdin($question):str_replace("\r","",eval("return ".get_stdin($question).";"))),
                 "reponse"=>str_replace("\r","",eval("return $question->reponse;")),
                 "url_retour"=>"index.php?p=serie&ID=".$question->serieID,
                 "titre_retour"=>"la liste de questions",
                 "suivante"=>$question->suivante,
                 "état_réussi"=>$avancement->get_etat()==Question::ETAT_REUSSI,
                 "validation"=>$question->code_validation,
                 "mode"=>get_mode($langid),
                 "lang_nom"=>LANG_NOMS[$langid]
    );
    
    return $infos;
}

function compter_lignes($texte){
    if($texte==""){
        return 0;
    }
    else{
        return count(preg_split('/\n/',$texte));
    }
}

function traiter_résultats($sortie_standard, $infos, $avancement, $question){
    $résultats=array();

    if(isset($_POST['submit'])){
        $résultats["essayé"]="true";
        if(valider_résultats($sortie_standard, $infos['reponse'])){
            sauvegarder_état_réussi($avancement, $infos['code']);
            $résultats["réussi"]="true";
        }
        else{
            sauvegarder_état_échec($avancement, $infos['code']);
            $résultats["nonréussi"]="true";
        }
    }
    $résultats["état_réussi"]=$avancement->get_etat()==Question::ETAT_REUSSI;

    return $résultats;
}

function valider_résultats($output, $reponse){
    return $reponse!="null" && $output==$reponse; //en PHP, "" == NULL (arg!!!)
}

function sauvegarder_état_réussi($avancement, $code){
    $avancement->set_reponse($code);
    $avancement->set_etat(Question::ETAT_REUSSI);
}

function sauvegarder_état_échec($avancement, $code){
    //Met la réponse à jour dans l'avancement seulement
    //si la question n'avait pas déjà été réussie
    if($avancement->get_etat()!=Question::ETAT_REUSSI){
        $avancement->set_reponse($code);
        $avancement->set_etat(Question::ETAT_NONREUSSI);
    }
}

function sauvegarder_état_non_réussi($avancement, $code){
    $avancement->set_etat(Question::ETAT_NONREUSSI);
}

function render_page($infos){
    $template=$GLOBALS['mustache']->loadTemplate("question_prog");
    echo $template->render($infos);
}


?>
