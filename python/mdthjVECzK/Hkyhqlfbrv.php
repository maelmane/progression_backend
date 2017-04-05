<?php

require('../quiz.php');

$lorem=explode(" ",'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.');
$mots=array_slice($lorem, rand(0,5), rand(7,12));
$phrase=implode(" ",$mots);

execute("Question 6", "Faites afficher le nombre de mots dans la phrase donnée.",sizeof($mots),'', "phrase=\"$phrase\"",'',"","", "6Q3xfktK2X.php");

?>
