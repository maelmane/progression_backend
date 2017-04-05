<?php

require('../quiz.php');

$lorem=explode(" ",'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.');
$mots=array_slice($lorem, rand(0,5), rand(7,12));
$phrase=implode(" ",$mots);

execute("Question 7", "Faites afficher la phrase donnée à l'envers.",strrev($phrase),'', "phrase=\"$phrase\"",'',"","", "QiTFdcGxUb.php");

?>
