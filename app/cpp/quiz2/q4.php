<?php

require('../quiz.php');

$r=rand(5, 25);

$a=array();
$a[0]= rand(999,99999);
$t=$a[0];
$sum=$a[0];
for($i=1;$i<$r;$i++){
   $a[i]= rand(999,99999);
   $t=$t . "+". $a[i];
   $sum+=$a[i];
}


execute("Question 4", "Implémentez la fonction additonner qui retourne le résultat de l'addition passée sous forme de chaîne de caractères.<br><br>Indice : stoi transforme une string en int.", "" . $sum, 'Bravo champion!', "
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
