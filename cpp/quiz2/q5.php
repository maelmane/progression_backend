<?php

require('../quiz.php');


$l_ch=strlen($_POST['stdin']);

execute("Question 5", "Implémentez la fonction encadrer qui retourne une chaîne encadrée par des astérisques en laissant un espace entre le texte et le cadre.<br><br>Remarque : la chaîne doit être saisie sans retour de chariot dans le champ «Entrée».", "**". str_repeat("*", $l_ch) ."**
* ". str_repeat(" ", $l_ch) ." *
* " . $_POST['stdin'] . " *
* ". str_repeat(" ", $l_ch) ." *
**". str_repeat("*", $l_ch) ."**", 'Bravo champion!', "
#include <iostream>
#include <string>

using namespace std;

string encadrer(string chaine);

int main(){
    string ma_chaine;

    cin >> ma_chaine;
    cout << encadrer(ma_chaine) << endl; 
}

string encadrer(string chaine){
","","}" );

?>
