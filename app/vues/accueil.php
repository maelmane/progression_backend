<?php

require_once('quiz_preambule.php');

page_header("Tableau de bord");
page_content();
page_footer();

function page_content(){
    display_header();
    display_themes();
    display_zone_pratique();
    display_footer();
}

function display_header(){
    echo "<h3>Exercices de programmation</h3>
              <pre class='code-wrapper'><code>
                  <table width=100%>
                      <th align=left  width=70%>Thème</th><th align=center width=30%>Complété</th>";
}

function display_themes(){
    foreach(get_themes() as $theme){
        echo         "<tr>
                          <td><a href='?p=theme&ID=$theme->id'>$theme->titre</td>
                          <td align=center>". calculer_avancement_theme($theme) . "% </a></td>
                      </tr>
         ";
    }
}

function display_zone_pratique(){
    echo             "<tr><td><br></td></tr>
                      <tr>
                          <td><a href='?p=pratique'>Zone de pratique libre</a></td>
                      </tr>";
}

function display_footer(){
    echo         "</table>
              </pre>
    ";
}

function calculer_avancement_theme($theme){
    return floor($theme->get_avancement($_SESSION['user_id'])/$theme->get_nb_questions()*100);
}
?>
