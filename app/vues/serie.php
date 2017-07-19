<?php

require_once('quiz_preambule.php');

if(!isset($_GET['ID'])){
        header('Location: index.php?p=accueil');
}

$serie=new Serie($_GET['ID'], $_SESSION['user_id']);
$serie->load_info();

if(is_null($serie->nom)){
        header('Location: index.php?p=accueil');
}

page_header();

echo "

        <h3>$serie->titre</h3>
        <pre class='code-wrapper'><code>
            <table width=100%>
";

foreach($serie->get_questions() as $question){
    echo "<tr>";
    if($question->etat == Question::ETAT_CACHE){
        echo "<td style='color: #777;'>⚪ N°". $question->numero ." : ". $question->titre ."</a>";
    }
    else{
        echo "<td><a href='?p=question&id=$question->id'>" . ($question->etat == Question::ETAT_REUSSI?"⚫":"⚪"). " N°". $question->numero ." : ". $question->titre ."</a>";
    }
    echo "</td>
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