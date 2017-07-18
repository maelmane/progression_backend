<?php

require_once('quiz_preambule.php');

$theme=new Theme($_GET['ID']);
$theme->load_info();

if(is_null($theme->nom)){
        header('Location: index.php?p=accueil');
}

page_header();

echo "

        <h3>$theme->titre</h3>
        <pre class='code-wrapper'><code>
            <table width=100%>
";

foreach($theme->get_series() as $serie){
    echo "
          <tr>
          <td><a href='?p=serie&ID=$serie->id'>Série ". $serie->numero ." : ". $serie->titre ."(".$serie->get_avancement()."/".$serie->get_nb_questions().")</a></td>
          </tr>

         ";
}

echo "
<td>
<br>
<br>
<a href='pratique'>Zone de pratique libre
</a></td>
<tr><td align=right><a href=index.php?p=accueil>Retour à l'accueil</a></td></tr>
</tr>
</table>
";

page_footer();

?>