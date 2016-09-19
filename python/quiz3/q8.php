<?php

require('../quiz.php');

$r=rand(100,200);
$s=($r-100)/100;

execute("Question 8", "Utilisez la fonction <code>entier_aléatoire</code> pour obtenir et afficher un entier choisi aléatoirement entre <em>100 et 200</em> inclusivement, sachant que <code>random.random</code> retourne un nombre réel choisi entre 0,0 et 1,0 inclusivement.", $r, "EeJ3t6fNNv", "
import random

def entier_aléatoire(max):
    \"\"\"
    Fournit un nombre entier pseudo-aléatoire sélectionné entre 0 et <em>max</em>.

    Paramètre :
    max : un nombre entier limite supérieure inclusive du tirage pseudo-aléatoire.

    Retourne : un nombre entier pseudo-aléatoire sélectionné entre 0 et <em>max</em>.

    \"\"\"
    nb_aléatoire = random.random()
    return round(max * nb_aléatoire)

", "", "", "
import random;random.random=lambda:".number_format($s,3)."
"
);

?>
