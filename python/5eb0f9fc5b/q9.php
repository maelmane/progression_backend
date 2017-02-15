<?php

require('../quiz.php');

$min=rand(10,100);
$max=rand(100,300);

$r=rand($min,$max);
$s=($r-$min)/($max-$min);

execute("Question 9", "Cette fonction <code>entier_aléatoire</code> est vraiment une bonne idée. Il serait encore mieux de lui donner un nouveau paramètre <code>min</code> délimitant le nombre pseudo-aléatoire minimum pouvant être retourné. Modifiez la fonction <code>entier_aléatoire</code> afin qu'elle retourne un nombre entier pseudo-aléatoire entre <code>min</code> et <code>max</code>.", $r, "P23zyNUkpL", "
from random import random

","
def entier_aléatoire(min, max):
    \"\"\"
    Fournit un nombre entier pseudo-aléatoire sélectionné entre min et max

    Paramètre :
    min : un nombre entier limite inférieure inclusive du tirage pseudo-aléatoire
    max : un nombre entier limite supérieure inclusive du tirage pseudo-aléatoire

    Retourne : un nombre entier pseudo-aléatoire sélectionné entre min et max

    \"\"\"
    nb_aléatoire = random()

", "
#Affiche un nombre pseudo-aléatoire choisi entre $min et $max.
print(entier_aléatoire($min,$max))", "
random=lambda:".number_format($s,3)."
"
);

?>
