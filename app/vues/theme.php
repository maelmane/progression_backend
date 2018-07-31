
<?php
require_once('quiz_preambule.php');

$theme=load_theme_or_quit();

page_header($theme->titre);
page_content($theme);
page_footer();


function page_content($theme){
    display_theme_header($theme);
    display_theme_content($theme);
}

function load_theme_or_quit(){
    $theme=new Theme($_GET['ID'], $_SESSION['user_id']);

    if(is_null($theme->id)){
        header('Location: index.php?p=accueil');
    }

    return $theme;
}

function display_theme_header($theme){
    echo"
         <h3>$theme->titre</h3>
         <br>
         $theme->description
         <br><br>
         ";
}

function display_theme_content($theme){
    series_header();
    foreach(get_series($theme->id) as $serie){
        series_row($serie);
    }
    series_footer();
}


function series_header(){
    echo "<div class='code-wrapper'>
              <table width=100%>
                  <th align=left width=10%>N°</th><th align=left>Sujet</th><th align=center>Complété</th>
         ";   
}    

function series_row($serie){
    echo         "<tr>
                      <td>". $serie->numero ."</td>
                      <td><a href='?p=serie&ID=$serie->id'>". $serie->titre ."</a></td>
                      <td align=center>".($serie->get_nb_questions()==0?"--":calculer_pourcentage_avancement($serie)) ."%</a></td>
                  </tr>";
}

function series_footer(){
    echo         "<tr>
                      <td> </td>
                  </tr>
                  <tr>
                      <td colspan=3 align=left><a href=index.php?p=accueil>↩ Retour à l'accueil</a></td></tr>
                  </tr>
              </table>
          </div>";
}

function calculer_pourcentage_avancement($serie){
    return floor($serie->get_avancement($_SESSION['user_id'])/$serie->get_nb_questions()*100);
}
?>
