<?php

require('../quiz.php');

$entree=explode("\r\n",$_POST["stdin"]);
if($entree[0]==0){
  $entree[0]=rand(0,999);
}
if($entree[1]==0){
  $entree[1]=rand(0,999);
}
execute("Question 8", "Faites afficher le résultat de la somme des entrées sous la forme «entrée1 + entrée2 = résultat» (par exemple «7 + 3 = 10»).", $entree[0]. " + " . $entree[1] . " = " . strval(intval($entree[0])+intval($entree[1])), '', "",'print(42)',"" ); 
?>
