<?php

require('../quiz.php');

$lorem=explode(" ",'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.');
$mots=array_slice($lorem, rand(0,5), rand(7,12));
$phrase=implode(" ",$mots);
$reponse=implode(" ",array_reverse($mots));

execute("Question 8", "Faites afficher les mots de la phrase donnée dans l'ordre inverse.",$phrase,'', "phrase=\"$reponse\"",'',"","", "iCbAwa1oj5.php");

?>
