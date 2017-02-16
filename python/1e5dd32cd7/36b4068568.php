<?php

require('../quiz.php');

$r=rand(0,1000);
execute("Question 5", "Faites afficher la valeur de a.", $r, 'oJOFTOFVIA', "a=$r",'print(42)', "");

?>
