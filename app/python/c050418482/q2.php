<?php

require('../quiz.php');

$r=rand(1000,9999);
$fonction="
def trier_liste( une_liste ):
    \"\"\"
    Tri une liste d'éléments comparables.

    Paramètres : 
    une_liste : une liste à trier d'éléments comparables entre eux (du même type ou de types comparables)

    Retour : une liste contenant les mêmes éléments que <em>une_liste</em> en ordre croissant.

    Exemples :
    >>> trier_liste([ 42, 2, 0, 21 ])
    [0, 2, 21, 42]
    >>> trier_liste([7])
    [7]
    >>> trier_liste([])
    []
    \"\"\"
    items = list(une_liste)
    for i in range(len(items)):
        for j in range(len(items)-1-i):
            if items[j] > items[j+1]:
                items[j], items[j+1] = items[j+1], items[j]     
    return items
    
def trier_liste_décroissant( une_liste ):
    \"\"\"
    Tri une liste d'éléments comparables en ordre décroissant.

    Paramètres : 
    une_liste : une liste à trier d'éléments comparables entre eux (du même type ou de types comparables)

    Retour : une liste contenant les mêmes éléments que <em>une_liste</em> en ordre décroissant.
    
    Exemples :
    >>> trier_liste_décroissant([ 42, 2, 0, 21 ])
    [42, 21, 2, 0]
    >>> trier_liste_décroissant([7])
    [7]
    >>> trier_liste_décroissant([])
    []
    \"\"\"
";

$exec_test='

#Exécute les tests
if __name__ == "__main__":
    import doctest
    doctest.testmod()';

execute("Question 2", "Les doctests de cette fonction doivent <em>passer</em>. Évitez le copier-coller!", "" ,'nLkEFD1wsc', $fonction, "    pass", $post_c. $exec_test, "", "");

?>

