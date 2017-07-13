<?php

require('../quiz.php');



$tab=rand(0,100);
$rep=$tab;
for ($i=0;$i<10;$i++){
  $tab=$tab . ", " . rand(0,100);
}
execute("Question 6", "Faites afficher la valeur du  premier élément du tableau <em>numeros</em>", $rep,'JBRzLdF0e6', "numeros=[$tab]",'', "");

?>
