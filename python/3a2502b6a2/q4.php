<?php

require('../quiz.php');

$entree=explode("\r\n",$_POST["stdin"]);
if($entree[0]==0){
  $entree[0]=rand(0,999);
}
if($entree[1]==0){
  $entree[1]=rand(0,999);
}

if($_POST['stdin']=='') $_POST['stdin']=$entree[0];
execute("Question 4", "Faites afficher l'entrée saisie au clavier.", $entree[0], '', "",'print(42)',"" ); 
?>
