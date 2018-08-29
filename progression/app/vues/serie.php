<?php

$serie=charger_serie_ou_terminer();
$theme=charger_theme_ou_terminer($serie);

page_header($theme->titre);
page_content($serie);

function charger_serie_ou_terminer(){
    $serie=new Serie($_GET['ID']);
    
    if(is_null($serie->id)){
        header('Location: index.php?p=accueil');
    }

    return $serie;
}

function charger_theme_ou_terminer($serie){
    $theme=new Theme($serie->themeID);

    if(is_null($theme->id)){
        header('Location: index.php?p=accueil');
    }

    return $theme;
}

function page_content($serie){
    $questions=$serie->get_questions();
    calculer_questions($questions);
    render_page($serie, $questions);
}

function calculer_questions($questions){
    calculer_url($questions);
    calculer_css($questions, $_SESSION['user_id']);
}

function calculer_url($questions){
    foreach($questions as $question){
        if($question->type==Question::TYPE_PROG){
            $question->page="question_prog";
        }
        elseif($question->type==Question::TYPE_SYS){
            $question->page="question_sys";
        }
        else{
            $question->page="erreur";
        }
    }
}

function calculer_css($questions, $user_id){
    foreach($questions as $question){
        if($question->get_avancement($user_id)->get_etat() == Question::ETAT_REUSSI){
            $question->css="questionReussie";
        }
        else{
            $question->css="questionNonReussie";
        }
    }
}

function render_page($serie, $questions){
    $template=$GLOBALS['mustache']->loadTemplate("serie");
    echo $template->render(array('serie'=>$serie, 'questions'=>$questions));
}
