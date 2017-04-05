<?php

require('../quiz.php');

$lorem=explode(" ",'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.');
$phrase=implode(" ",array_slice($lorem, rand(0,5), rand(7,12)));

execute("Question 5", "Faites afficher la phrase donnée avec le premier caractère en majuscules.",ucfirst($phrase),'', "phrase=\"$phrase\"",'',"","", "Hkyhqlfbrv.php");

?>
