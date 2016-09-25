<?php

require('../quiz.php');

$r=rand(0,100);
$n=rand(2, 20);
$rep=sprintf("%.5e",pow($r,$n));
execute("Question 3", "Faites une fonction qui calcule et retourne la puissance n d'un nombre réel", $rep, 'oJOFTOFVIA', "
#include <iostream>

using namespace std;

float puissance(float nombre, float n){
",
"    return 0;",
"
}

int main(){
    cout << puissance($r, $n);
}
"
);

?>
