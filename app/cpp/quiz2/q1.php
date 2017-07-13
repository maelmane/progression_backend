<?php

require('../quiz.php');

$r=rand(5, 25);
$a="" . rand(0,999);

$seed = str_split('abcdefghijklmnopqrstuvwxyz'
                 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                 .'0123456789!@#$%^&*'); // and any other characters
shuffle($seed); // probably optional since array_is randomized; this may be redundant

foreach (array_rand($seed, $r) as $k) $a .= $seed[$k];

foreach(str_split($a) as $t){
  if($t>='a' and $t<='z' or $t>='A' and $t<='Z'){
      $p=$t;
      break;
  }
}

execute("Question 1", "Implémentez la fonction premiere_lettre qui retourne la première lettre (minuscule ou majuscule) d'une string.", "$p", 'Bravo champion!', "
#include <iostream>
#include <string>

using namespace std;

char premiere_lettre(string chaine);

int main(){
    string ma_chaine(\"$a\");
    cout << premiere_lettre(ma_chaine) << endl; 
}

char premiere_lettre(string chaine){
","","}" );

?>
