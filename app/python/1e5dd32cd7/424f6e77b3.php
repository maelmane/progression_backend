<?php

require('../quiz.php');

$tab=rand(0,100);
for ($i=0;$i<8;$i++){
  $tab1=$tab1 . ", " . rand(0,100);
}

$rep="[" . substr("$tab1]",2);
$tab=$tab . $tab1;
for ($i=0;$i<10;$i++){
  $tab=$tab . ", " . rand(0,100);
}
execute("Question 8", "Faites afficher sous forme de tableau les éléments 1 à 8 de <em>numeros</em>",$rep,'HyDyhqlbwZ', "numeros=[$tab]",'', "");

?>
