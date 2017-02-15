<?php

require('../quiz.php');

$r=rand(10,50);

execute("Question 10","Faites une fonction appelée <code>factorielle</code> permettant de calculer la factorielle de n'importe quel nombre entier selon la signature donnée.", gmp_fact($r), "Q8TjuJosy4", "","
def factorielle(x):
    \"\"\"
    Calcule la factorielle de x

    Calcule et retourne la factorielle de x, c'est-à-dire x * x-1 * x-2 * … * 1

    Paramètre :
    x : un nombre entier

    Retourne : a factorielle du nombre x

    \"\"\"

",
"
#Affiche la factorielle du nombre $r
print(factorielle($r))"
);

?>
