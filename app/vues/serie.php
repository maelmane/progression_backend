<?php

require_once('quiz_preambule.php');

if(!isset($_GET['ID'])){
        header('Location: index.php?p=accueil');
}

$serie=new Serie($_GET['ID']);

if(is_null($serie->id)){
        header('Location: index.php?p=accueil');
}

$theme=new Theme($serie->themeID);

page_header($theme->titre);

echo "

        <h3>$serie->titre</h3>
        <br>
        $serie->description
        <br><br>
        <pre class='code-wrapper'><code>
            <table width=100%>
              
";

//Questions
$ids=get_questions($serie->id);
foreach($ids as $question){
    $avcmt=$question->get_avancement($_SESSION["user_id"]);
    echo "<tr>";
    if($avcmt->get_etat() == Question::ETAT_CACHE){
        echo "<td style='color: #777;'>⚪ N°". $question->numero ." : ". $question->titre ."</a>";
    }
    else{
        $page=$question->type == Question::TYPE_PROG?"question_prog":"question_sys";
        echo "<td".($avcmt->get_etat() != Question::ETAT_REUSSI?" class='questionNonReussie' ":"").">" . $question->numero ."</td><td><a".($avcmt->get_etat() != Question::ETAT_REUSSI?" class='questionNonReussie' ":"")." href='?p=$page&ID=$question->id'>". $question->titre ."</a></td>";
        }

    echo "</td>
          </tr>
         ";
}

echo "
<tr>
<td colspan=3 align=right><a href=index.php?p=theme&ID=$serie->themeID >Retour à la liste des sujets</a></td>
</tr>
</tr>
</table>
";

page_footer();

?>
