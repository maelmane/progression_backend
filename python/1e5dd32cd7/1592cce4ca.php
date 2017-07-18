<?php

require('../quiz.php');

$tab=rand(0,100);
for ($i=0;$i<rand(3,10);$i++){
  $tab=$tab . ", " . rand(0,100);
}

$tab1=rand(0,100);
for ($i=0;$i<3;$i++){
  $tab1=$tab1 . ", " . rand(0,100);
}

$rep="[$tab1]";
execute("Question 8", "Faites afficher sous forme de tableau les 4 derniers éléments de <em>numeros</em>",$rep,'', "numeros=[$tab,  $tab1]",'',"","", "3d57a52c4e.php");

?>
