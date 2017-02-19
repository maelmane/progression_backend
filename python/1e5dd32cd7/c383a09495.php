<?php
require '../quiz.php';

$nb_i=rand(500,1000);
$nb_somme=0;
$t="[";
for($i=0;$i<$nb_i;$i++){
    $nb_r=strval(rand(0,999));
    $nb_somme+=$nb_r;
    $t=$t . $nb_r . ", ";
}
chop($t,", ");
$t=$t . "]";

execute("Question 3","Soit le tableau nommé <em>tableau</em> ci-dessous. Faites un programme qui calcule et affiche la somme de tous les éléments que contient le tableau <em>tableau</em>.", "$nb_somme", '',"tableau=$t","print(0)","",'','2ade3276eb.php');
?>
