<?php

require('../quiz.php');

$r=rand(0,1000);
$s=rand(0,1000);
$reponse=$r+$s;
execute("Question 3", "Faites afficher la phrase «La somme de $r et $s est $reponse ».", "La somme de $r et $s est $reponse", '', "alpha=$r\nbeta=$s",'print(42)',"" ); 
?>
