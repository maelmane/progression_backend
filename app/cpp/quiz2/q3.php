<?php

require('../quiz.php');

$r=rand(5, 25);

$a= rand(999,99999);
$b= rand(999,99999);
$t= $a . "+" . $b;


execute("Question 3", "Implémentez la fonction additonner qui retourne le résultat de l'addition passée sous forme de chaîne de caractères.<br><br>Indice : stoi transforme une string en int.", "" . $a+$b, 'Bravo champion!', "
#include <iostream>
#include <string>

using namespace std;

int additionner(string chaine);

int main(){
    string ma_chaine(\"$t\");
    cout << additionner(ma_chaine) << endl; 
}

int additionner(string chaine){
","","}" );

?>
