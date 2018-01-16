<?php

require_once('quiz_preambule.php');

$theme=new Theme($_GET['ID'], $_SESSION['user_id']);
$theme->load_info();

if(is_null($theme->id)){
        header('Location: index.php?p=accueil');
}

page_header($theme->titre);

echo "

        <h3>$theme->titre</h3>
        <br>
        $theme->description
        <br><br>
        <pre class='code-wrapper'><code>
            <table width=100%>
              <th align=left width=10%>N°</th><th align=left>Sujet</th><th align=center>Complété</th>
";

//Description
//echo "<tr><td>$theme->description<td></tr>";

//Séries
foreach($theme->get_series() as $serie){
    echo "
          <tr>
          <td>". $serie->numero ."</td><td><a href='?p=serie&ID=$serie->id'>". $serie->titre ."</a></td><td align=center>".($serie->get_nb_questions()>0?floor($serie->get_avancement()/$serie->get_nb_questions()*100):"--") ."%</a></td>
          </tr>

         ";
}

echo "
<td>
<br>
</td>
<tr><td colspan=3 align=right><a href=index.php?p=accueil>Retour à l'accueil</a></td></tr>
</tr>
</table>
";

page_footer();

?>