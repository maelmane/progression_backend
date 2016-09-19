<?php

require('../quiz.php');


$fonction='
def chaméliser( phrase ): 
    """
    Donne la version en «casse de chameau» (<em>camel case</em>) d\'une phrase.

    Retourne une copie de la phrase originale dont chaque espace a été
    supprimée et dont chaque mot commence par une majuscule sauf le
    premier.

    Paramètres :
    phrase : une chaîne de caractères à «chaméliser».

    Retour : une copie de la phrase originale «chamélisée».

    >>> chaméliser("")
    \'\'
    >>> print(chaméliser("salut"))
    salut
    >>> print(chaméliser("salut tout le monde"))
    salutToutLeMonde
    >>> print(chaméliser("Salut Tout le Monde"))
    salutToutLeMonde
    >>> print(chaméliser("Salut Tout le Monde "))
    salutToutLeMonde

    """
';

$exec_test='
#Exécute les tests
if __name__ == "__main__":
    import doctest
    doctest.testmod()';

execute("Question 4", 'Les doctests de cette fonction doivent <em>passer</em>. ', "" ,'O1v5GCuN2x', $fonction, '    pass', $exec_test, "", "");

?>

