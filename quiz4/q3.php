<?php

require('../quiz.php');


$fonction='
def racine_nième( opérande, racine ):
    """
    Calcule la racine nième d\'un nombre.

    Paramètres :
    opérande : le nombre réel duquel il faut extraire la racine nième.
    racine : un nombre réel, la racine à extraire (ex. 2 pour la 
             racine carrée, 3 pour la racine cubique, etc.)

    Retour : un nombre réel, la racine nième de l\'opérande.

    >>> racine_nième( 81, 2 )
    9.0
    >>> (racine_nième( 1000, 3)-10)<0.00001
    True
    >>> racine_nième( 1024, 10)
    2.0

    """
';

$exec_test='
#Exécute les tests
if __name__ == "__main__":
    import doctest
    doctest.testmod()';

execute("Question 3", 'Les doctests de cette fonction doivent <em>passer</em>. Souvenez-vous que <img class="mwe-math-fallback-image-inline tex" alt="\sqrt[n]{x} \,=\, x^{1/n}" src="//upload.wikimedia.org/math/f/6/e/f6e2875466bdf9e5eb4a7db071b8e812.png" />', "" ,'owu0emFrvb', $fonction, '    pass', $exec_test, "", "");

?>

