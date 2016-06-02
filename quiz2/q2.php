<?php

require('../quiz.php');

$tab=rand(0,100);
for ($i=0;$i<2;$i++){
  $tab=$tab . ", " . rand(0,100);
}
$rep="[$tab]";
for ($i=0;$i<10;$i++){
  $tab=$tab . ", " . rand(0,100);
}

execute("Question 3", "Faites afficher sous forme de tableau les 3 premiers éléments de <em>numeros</em>", $rep,'hqL5f2Ktf3', "numeros=[$tab]",'', "");

?>
