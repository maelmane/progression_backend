<?php

require('../quiz.php');

$n=rand(2, 100);
$rep=pow(2,$n);
execute("Question 2", "Faites une fonction qui calcule et retourne la <em>n</em>ième puissance de 2 où n est un nombre entier entre 0 et 63.", $rep, '7UAmyogW4n', "
#include <iostream>

using namespace std;

long puissance_de_2(int n){
",
"    return 0;",
"
}

int main(){
    cout << puissance_de_2($n);
}
"
);

?>
