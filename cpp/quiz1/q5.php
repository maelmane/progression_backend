<?php

require('../quiz.php');

execute("Question 5", "Faites une fonction qui correspond à la déclaration donnée.", "Bravo les cocos!!!", 'UxbRMDqr0c', "
#include <iostream>
#include <cstring>

using namespace std;

/*
Joint deux chaînes de caractères dans une troisième en y plaçant un caractère de chaine1 puis de chaine2 puis chaine1, etc.

Paramètres :
  dest    : pointeur vers une chaîne de caractère initialisée à la longueur totale de chaine1+chaine2.
  chaine1 : première chaîne à zipper.
  chaine2 : deuxième chaîne à zipper.

Retour :
  un pointeur vers la chaîne dest.
*/
char *zipper(char *dest, const char *chaine1, const char *chaine2);

int main(){
    char secret_1[] = \"Baolsccs!\";
    char secret_2[] = \"rv e oo!!\";
    char *resultat = new char[strlen(secret_1)+strlen(secret_2)];

    cout << zipper(resultat, secret_1, secret_2) << endl;

}
",""
);

?>
