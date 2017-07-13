<?php

require('../quiz.php');

$r=rand(0,1000);
execute("Question 0", "Faites afficher «Bonjour le monde!».", "Bonjour le monde!", '', "",'print(42)',"" );

?>
