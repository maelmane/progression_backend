<?php

require('../quiz.php');

$r1=rand(0,1000);
$r2=rand(0,1000);
execute("Question 3", "Utilisez les fonctions <code>afficher_nb1</code> et <code>afficher_nb2</code> pour faire afficher les deux nombres dans l'ordre le  nombre 2 puis le  nombre 1", "$r2\n$r1", "YIB8dFFoy5", "
def afficher_nb1():
    \"\"\"
    Affiche un nombre entier «magique».

    Affiche un nombre entier «magique», c'est à dire un nombre codé en dur
    dans le code source et sans signification évidente.

    \"\"\"
    nombre = $r1
    print(nombre)

def afficher_nb2():
    \"\"\"
    Affiche un nombre entier «magique».

    Affiche un nombre entier «magique», c'est à dire un nombre codé en dur
    dans le code source et sans signification évidente.

    \"\"\"
    nombre = $r2
    print(nombre)

"
);

?>
