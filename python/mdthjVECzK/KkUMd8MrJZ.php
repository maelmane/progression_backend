<?php

require('../quiz.php');

$lorem=explode(" ",'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.');
$mots=array_slice($lorem, rand(0,5), rand(7,12));
$phrase=implode(" ",$mots);

execute("Question 4", "Faites afficher le premier et le dernier caractère de la phrase donnée.",$phrase[0] . substr($phrase,-1),"","phrase=\"$phrase\"", "","", "", "gZ6Y9HfNO.php");

?>
