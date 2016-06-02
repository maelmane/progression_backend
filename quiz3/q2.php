<?php

require('../quiz.php');

$r1=rand(0,1000);
execute("Question 2", "Utilisez la fonction <code>afficher_nb</code> pour faire afficher 10 fois le nombre <code>nombre</code>.", "$r1\n$r1\n$r1\n$r1\n$r1\n$r1\n$r1\n$r1\n$r1\n$r1", "IOZ63fBbRh", "
def afficher_nb():
    \"\"\"
    Affiche un nombre entier «magique».

    Affiche un nombre entier «magique», c'est à dire un nombre codé en dur
    dans le code source et sans signification évidente.

    \"\"\"
    nombre = $r1

    print(nombre)

"
);

?>
