<?php

require('../quiz.php');

$r=rand(2, 20);
$n=array();
$ns=rand(0,$r);
for($a=0;$a<$r;$a++) $n[$a]=rand(0,1000);

execute("Question 4", "Faites une fonction qui correspond à la déclaration donnée.", array_sum(array_slice($n,0,$ns)), 'dtIgYoHdlo', "
#include <iostream>

using namespace std;

/*
Calcule et retourne la somme des n éléments du tableau passé en paramètre.

Paramètres :
  nombres : tableau d'entiers.
  n : le nombre d'éléments dans le tableau.

Retour :
  la somme des n premiers entiers du tableau.
*/
long somme(const int *nombres, int n);

int main(){
    int entiers_aleatoires[]={" . implode(', ',$n) . "};
    cout << somme(entiers_aleatoires, " . $ns . ");
}
",""
);

?>
