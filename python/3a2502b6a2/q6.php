<?php

require('../quiz.php');

$entree=explode("\n",$_POST["stdin"]);
if($entree[0]==''){
  $entree[0]=rand(0,999);
}

if($_POST['stdin']=='') $_POST['stdin']=$entree[0];


execute("Question 6", "Faites afficher le double de la valeur numérique saisie au clavier.", intval($entree[0])*2, '', "",'print(42)',"" ); 
?>
