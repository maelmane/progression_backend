<?php

require_once('quiz_preambule.php');

page_header("Tableau de bord");

echo "
		  <h3>Exercices de programmation</h3>

		  <pre class='code-wrapper'><code>
		      <table width=100%>
              <th align=left  width=70%>Thème</th><th align=center width=30%>Complété</th>
";

foreach(get_themes($_SESSION['user_id']) as $theme){
    echo "
			<tr>
			  <td><a href='?p=theme&ID=$theme->id'>$theme->titre</td><td align=center>". floor($theme->get_avancement()/$theme->get_nb_questions()*100) . "% </a></td>
			</tr>
         ";
}
echo "

            <tr>
              <td>
                <br><br>
                <a href='?p=pratique'>Zone de pratique libre</a></td>
            </tr>

		      </table>
    ";

page_footer();

?>
