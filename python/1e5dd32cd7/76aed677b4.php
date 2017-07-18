<?php
require '../quiz.php';

$nb_i=rand(500,1000);
$nb_pair=0;
$t="[";
for($i=0;$i<$nb_i;$i++){
    $nb_r=strval(rand(0,999));
    if($nb_r%2==0) $nb_pair++;
    $t=$t . $nb_r . ", ";
}
chop($t, ", ");
$t=$t . "]";

execute("Question 2","Soit le tableau nommé <em>tableau</em> ci-dessous. Faites un programme qui calcule et affiche le nombre d'éléments <em>pairs</em> que contient le tableau <em>tableau</em>.", "$nb_pair", '',"tableau=$t","print(0)", "",'','c383a09495.php');
?>
