<?php

require('../quiz.php');


$fonction='
def additionner(nombre1, nombre2):
    """
    Additionne deux nombres.

    Paramètres :
    nombre1 : nombre réel à additionner
    nombre2 : nombre réel à additionner

    Retour : La somme de nombre1 et nombre2.

    Exemples :
    >>> additionner(2, 3) 
    5
    >>> additionner(-2.1, 1)
    -1.1
    >>> additionner(-1, -3.0)
    -4.0

    """
';

$exec_test='
#Exécute les tests
if __name__ == "__main__":
    import doctest
    doctest.testmod()';

execute("Question 2", "Les doctests de cette fonction doivent <em>passer</em>", "" ,'CMYImZvAPq', $fonction, '    pass', $exec_test, "", "");

?>

