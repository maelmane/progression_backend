<?php

require('../quiz.php');

$r=rand(1000,9999);
$fonction="
from math import sqrt
from random import randrange 

def est_premier(nombre): 
    \"\"\" 
    Détermine si un nombre est premier.

    Paramètres : 
    nombre : un nombre entier potentiellement premier.

    Retour : Vrai si et seulement si <em>nombre</em> est premier.

    Exemples :
    >>> est_premier(7918) 
    False
    >>> est_premier(7919)
    True
    >>> est_premier(0)
    False

    \"\"\" 
    if nombre<2 : return False
    for i in range(2, int(sqrt(nombre))+1): 
        if nombre%i==0 : return False
    return True

def trouver_nb_premier(min, max):
    \"\"\"
    Fournit un nombre premier entre les limites [min, max[.

    Paramètres:
    min : un nombre entier limite inférieure inclusive de l'intevalle des nombres premiers possibles  
    max : un nombre entier limite supérieure exclusive de l'intevalle des nombres premiers possibles  

    Retour : un nombre premier sélectionné au hasard n tel que min <= n < max. Retourne None si max<=min. 

    >>> trouver_nb_premier(8, 13)
    11
    >>> trouver_nb_premier(5, 1) is None
    True
    >>> trouver_nb_premier(5, 5) is None
    True
    >>> est_premier(trouver_nb_premier(1000000, 2000000))
    True
    >>> n = trouver_nb_premier(1000000, 2000000)
    >>> n >= 1000000 and n < 2000000
    True
    >>> trouver_nb_premier(1000000, 2000000) != trouver_nb_premier( 1000000, 2000000 )
    True

    \"\"\"
";


$exec_test='

#Exécute les tests
if __name__ == "__main__":
    import doctest
    doctest.testmod()';

execute("Question 3", "Les doctests de cette fonction doivent <em>passer</em>", "" ,'80fQM7KeS6', $fonction, "    return 0", $post_c. $exec_test, "", "");

?>

