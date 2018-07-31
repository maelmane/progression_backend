<?php

require_once('quiz_preambule.php');

$serie = load_serie_or_quit();
$theme = load_theme_or_quit($serie);

page_header($theme->titre);
page_content($serie);
page_footer();


function load_serie_or_quit(){
    if(!isset($_GET['ID'])){
        header('Location: index.php?p=accueil');
    }

    return new Serie($_GET['ID']);
}

function load_theme_or_quit($serie){
    if(is_null($serie->id)){
        header('Location: index.php?p=accueil');
    }

    return new Theme($serie->themeID);
}

function page_content($serie){
    display_serie_header($serie);
    display_serie_content($serie);
    display_serie_footer($serie);
}

function display_serie_header($serie){
    echo "<h3>$serie->titre</h3>
          <br>
          $serie->description
          <br><br>";
}
 
function display_serie_content($serie){
    $ids=get_questions($serie->id);
    question_header();
    foreach($ids as $question){
        question_row($question);
    }
    question_footer($serie);
}

function question_header(){
    echo "<div class='code-wrapper'>
              <table width=100%>";
}

function question_row($question){
    $avancement=$question->get_avancement($_SESSION["user_id"]);
    
    if($avancement->get_etat() == Question::ETAT_CACHE){
        display_question_cachee($question);
    }
    else{
        display_question_montree($question);
    }
}

function display_question_cachee($question){
    echo         "<tr>
                      <td class='questionCachee'>⚪ N°". $question->numero ." : ". $question->titre ."</a>
                  </tr>";
}

function display_question_montree($question){
    $page=$question->type == Question::TYPE_PROG?"question_prog":"question_sys";
    echo         "<tr>
                      <td class='".get_css_class($question)."'><a href='?p=$page&ID=$question->id'>". $question->numero ."</a></td>
                      <td class='".get_css_class($question)."'><a href='?p=$page&ID=$question->id'>". $question->titre ." </a></td>
                  </tr>";
     
}
        
function question_footer($serie){
    echo         "<tr>
                      <td> </td>
                  </tr>
                      <td colspan=3 align=left><a href=index.php?p=theme&ID=$serie->themeID >↩ Retour à la liste des sujets</a></td>
                 </tr>
             </table>
         </div>";
}

function get_css_class($question){
    $avancement=$question->get_avancement($_SESSION["user_id"]);
    
    return $avancement->get_etat() != Question::ETAT_REUSSI?"questionNonReussie":"questionReussie";
}

?>
