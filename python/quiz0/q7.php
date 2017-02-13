<?php

require('../quiz.php');

$entree=explode("\n",$_POST["stdin"]);
if($entree[0]==0){
  $entree[0]=rand(0,999);
}
if($entree[1]==0){
  $entree[1]=rand(0,999);
}
execute("Question 7", "Faites afficher la somme du double de la première valeur numérique saisie au clavier et de trois fois la deuxième.", intval($entree[0])*2+intval($entree[1])*3, '', "",'print(42)',"" ); 
?>
