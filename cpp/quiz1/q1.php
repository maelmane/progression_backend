<?php

require('../quiz.php');

$r=rand(0,1000);
execute("Question 1", "Faites une fonction qui calcule et retourne le carré d'un nombre entier", $r*$r, 'vs5u663JC5', "
#include <iostream>

using namespace std;

int carre(int nombre){
",
"    return 0;",
"
}

int main(){
    cout << carre($r);
}
"
);

?>
