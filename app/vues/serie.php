<?php

require_once('quiz_preambule.php');

if(!isset($_GET['ID'])){
        header('Location: index.php?p=accueil');
}

$serie=new Serie($_GET['ID']);
$serie->load_info();

page_header();

echo "

        <h3>$serie->titre</h3>
        <pre class='code-wrapper'><code>
            <table width=100%>
";

foreach($serie->get_questions() as $question){
    echo "
          <tr>
          <td><a href='?p=question&id=$question->id'>N°". $question->numero ." : ". $question->titre ."</a></td>
          </tr>

         ";
}

echo "
<tr>
<td align=right><a href=index.php?p=theme&ID=$serie->themeID >Retour à la liste des sujets</a></td>
</tr>
</tr>
</table>
";

page_footer();

?>