<?php

require('../quiz.php');

$r=rand(5, 25);

$seed = str_split('abcdefghijklmnopqrstuvwxyz'
                 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                 ); // and any other characters
shuffle($seed); // probably optional since array_is randomized; this may be redundant

foreach (array_rand($seed, $r) as $k) $a .= $seed[$k];
$n= rand(999,99999);
$a= $a . "/" . $n . "/";
foreach (array_rand($seed, $r) as $k) $a .= $seed[$k];

execute("Question 2", "Implémentez la fonction extraire_numero qui retourne le numéro contenu dans une string entre deux barres obliques.<br><br>Indice : la méthode find permet de chercher un caractère spécifique dans une chaîne.", "$n", 'Bravo champion!', "
#include <iostream>
#include <string>

using namespace std;

string extraire_numero(string chaine);

int main(){
    string ma_chaine(\"$a\");
    cout << extraire_numero(ma_chaine) << endl; 
}

string extraire_numero(string chaine){
","","}" );

?>
