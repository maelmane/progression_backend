<?php

require_once('quiz_preambule.php');
require_once('helpers.php');
require_once('prog.php');

$question=load_question_or_quit();
$avancement=load_avancement_or_quit();

$infos=array("langid"=>get_lang_defaut(),
             "code"=>get_code($question, $avancement),
             "params"=>get_params($question),
             "stdin"=>get_stdin($question),
             "setup"=>exécute_composantes($question->setup),
             "stdin"=>exécute_composantes($question->stdin),
             "enonce"=>exécute_composantes($question->enonce),
             "pre_exec"=>exécute_composantes($question->pre_exec),
             "pre_code"=>exécute_composantes($question->pre_code),
             "post_code"=>exécute_composantes($question->post_code),
             "reponse"=>exécute_composantes($question->respons),
             "lang_id"=>$qst->lang);

page_header();
prog_header($infos['langid']); //Attention! deuxième section <head>
page_contenu($infos, $avancement, $question);
prog_footer();
page_footer();

function prog_header($langid){
    inclusions_codemiror();
    set_mode_éditeur($langid);
    prog_header_fermeture();
}

function page_contenu($infos, $avancement, $question){
    afficher_intro($question->titre, $enonce);
    afficher_formulaire($infos);
    $output=exécuter_code($infos);
    afficher_sorties($output);
    afficher_résultats($output, $infos, $avancement, $question); 
}

function load_question_or_quit(){
    if(!isset($_GET['ID'])){
        header('Location: index.php?p=accueil');
    }
    
    return new QuestionProg($_GET['ID']);
}

function load_avancement_or_quit(){
    if(!isset($_GET['ID'])){
        header('Location: index.php?p=accueil');
    }

    return new Avancement($_GET['ID'], $_SESSION['user_id']);
}

function exécute_composante($composante){
    return str_replace("\r","",eval("return \"$composante\";"));
}

function afficher_résultats($output, $infos, $avancement, $question){
    afficher_résultats_header($infos);
    afficher_résultats_retour_série($question->serieID);
    $réussi=valider_résultats($output, $infos);
    sauvegarder_état($avancement, $infos['code'], $réussi);
    if($réussi){
        afficher_réussite($question->code_validation);
    }
    else{
        afficher_échec();
    }
    afficher_question_suivante($avancement, $question);
    afficher_résultats_footer();
}

function afficher_résultats_header($question);{
    echo "<table width=100%>";
}
function afficher_résultats_retour_série($serie_id){
    echo "    <tr>
                  <td align=left width=25%>
                      <a href=index.php?p=serie&ID=$serie_id>↩ Retour à la liste de questions</a>
                  </td>";
}

function valider_résultats($output, $reponse){
    //Vérifie la réponse
    return $reponse!="null" && $output==$reponse; //en PHP, "" == NULL (arg!!!)
}

function sauvegarder_état($avancememt, $code, $réussi){
    //Met la réponse à jour dans l'avancement
    if($réussi){
        $avancement->set_reponse($code);
        $avancement->set_etat(Question::ETAT_REUSSI);
    }
    else{
        if($avancement->get_etat()!=Question::ETAT_REUSSI){
            //Met la réponse à jour dans l'avancement
            $avancement->set_reponse($code);
            $avancement->set_etat(Question::ETAT_NONREUSSI);
        }
    }
}

function afficher_réussite($validation){
    echo "        <td width=50% align=center>
                      Bonne réponse!".((!is_null($validation)&&trim($validation!=""))?"
                  </td>
                  <td>
                      Code de validation : $validation":"")."
                  </td>";
}

function afficher_échec(){
    echo "        <td width=50% align=center>
                      Raté! Essayez encore
                  </td>";
}

function afficher_question_suivante($avancement, $question){
    echo "        <td align=right width=25%>";
    if($avancement->get_etat()==Question::ETAT_REUSSI and !is_null($question->suivante)){
        echo "        <a href=index.php?p=question_prog&ID=$qst->suivante>Question suivante →</a>";
    }
    echo "        </td>";
}


function afficher_résultats_footer(){
    echo "    </tr>
          </table>";
}
?>
