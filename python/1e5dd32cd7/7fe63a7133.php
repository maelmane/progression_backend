<?php
require '../quiz.php';

$nb_i=rand(500,1000);
$t="[";
for($i=0;$i<$nb_i;$i++){
    $t=$t . strval(rand(0,999)) . ", ";
}
$t=$t . strval(rand(0,999)) . "]";
execute("Question 1","Soit le tableau nommé <em>tableau</em> ci-dessous. Faites un programme qui calcule et affiche le nombre d'éléments que contient le tableau <em>tableau</em>.", $nb_i+1, '',"tableau=$t", "print(0)","","","76aed677b4.php");
?>
