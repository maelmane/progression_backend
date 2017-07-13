<?php

require('../quiz.php');

$r=rand(0,1000);
$reponse=2*$r;
execute("Question 2", "Faites afficher le double de la variable alpha.", "$reponse", '', "alpha=$r",'print(42)',"" ); 
?>
