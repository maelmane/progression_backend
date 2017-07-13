<?php

require('../quiz.php');

echo 

$n=rand(4,8);
$tab=rand(0,100);
for ($i=0;$i<$n;$i++){
  $num=rand(0,100);
  $tab=$tab . ", " . $num;
}
$rep=$tab . ", 17, 42, 25, ";
$tab1=rand(0,100);
for ($i=0;$i<$n;$i++){
  $num=rand(0,100);
  $tab1=$tab1 . ", " . $num;
}
$rep=$rep . $tab1;

$rep="[$rep]";
execute("Question 12", "Insérez les nombres 17, 42 et 25 au milieu de <em>numeros</em> puis faites afficher tous ses éléments sous forme de tableau, sachant que le tableau <em>numeros</em> est le taille variable.", $rep,'EBYOEVTAtv', "numeros=[$tab, $tab1]",'',"");

?>
