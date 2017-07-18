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
execute("Question 9", "Faites afficher sous forme de tableau tous les éléments de <em>numeros</em> dans l'ordre inverse.", $rep,'', "numeros=[$tab]",'',"","", "9642a5bf29.php");

?>
