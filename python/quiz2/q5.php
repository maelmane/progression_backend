<?php

require('../quiz.php');

$tab=rand(0,100);
$rep=$tab;
for ($i=0;$i<rand(3,10);$i++){
  $num=rand(0,100);
  $tab=$tab . ", " . $num;
  $rep=$num . ", " . $rep;
}

$rep="[$rep]";
execute("Question 6", "Faites afficher sous forme de tableau tous les éléments de <em>numeros</em> dans l'ordre inverse.", $rep,'9Z6sQkqapX', "numeros=[$tab]",'',"");

?>
