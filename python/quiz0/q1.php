<?php

require('../quiz.php');

$r=rand(0,1000);
$s=rand(0,1000);
$somme=$r+$s;
execute("Question 1", "Faites afficher la somme des nombres alpha et beta.", "$somme", '', "alpha=$r\nbeta=$s",'print(42)',"" );

?>
