<?php

require('../quiz.php');

$entree=explode("\r\n",$_POST["stdin"]);
if($entree[0]==''){
  $entree[0]=rand(0,999);
}

if($_POST['stdin']=='') $_POST['stdin']=$entree[0];

execute("Question 5", "Répétez sur trois lignes l'entrée saisie au clavier.", "$entree[0]\n$entree[0]\n$entree[0]", '', "",'print(42)',"" ); 
?>
