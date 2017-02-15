<?php

require('../quiz.php');

$entree=$_POST["stdin"];
execute("Question 6", "Faites afficher le double de la valeur numérique saisie au clavier.", intval($entree)*2, '', "",'print(42)',"" ); 
?>
