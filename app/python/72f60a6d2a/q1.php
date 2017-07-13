<?php

require('../quiz.php');


$fonction='
def allo_le_monde():
    """
    Affiche un mot de bienvenue au monde «Allo le monde!»

    Exemples :
    >>> allo_le_monde()
    Allo le monde!

    """
';

$exec_test='
#Exécute les tests
if __name__ == "__main__":
    import doctest
    doctest.testmod()';

execute("Question 1", "Les doctests de cette fonction doivent <em>passer</em>", "" ,'8SFZfPaWj6', $fonction, '    pass', $exec_test, "", "");

?>

