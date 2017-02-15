<?php

require('../quiz.php');

$r=rand(0,9999);
$fonction="
def numéro_magique(): 
    \"\"\" 
    Retourne un numéro magique entre 0 et 9999. 

    Exemples :
    >>> numéro_magique() 
    $r

    \"\"\" 
    numéro_magique=$r
";

$exec_test='
#Exécute les tests
if __name__ == "__main__":
    import doctest
    doctest.testmod()';

execute("Question 1", "Les doctests de cette fonction doivent <em>passer</em>", "" ,'GIT29x0vUh', $fonction, "    return 0", $exec_test, "", "");

?>

