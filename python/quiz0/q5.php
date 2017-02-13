<?php

require('../quiz.php');

$entree=explode("\r\n",$_POST["stdin"])[0];
execute("Question 5", "Répétez sur trois lignes l'entrée saisie au clavier.", "$entree\n$entree\n$entree", '', "",'print(42)',"" ); 
?>
