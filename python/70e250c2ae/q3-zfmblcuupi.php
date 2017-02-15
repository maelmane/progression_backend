<?php

require('../quiz.php');

$r=rand(0,99);
$s=rand(0,99);
$t=rand(0,99);
$u=rand(0,99);

execute("Question 3", "Ajoutez maitenant un mutateur <code>set_combinaison</code> qui prend en paramètre une liste d\'exactement 4 entiers entre 0 et 99 inclusivement. Assurez-vous que toutes ces conditions seront toujours respectées.", "", "clé1", '
import random

class CoffreFort:
    """
    Un coffre-fort très peu sécuritaire.

    """
    def __init__(self, une_combinaison):
        """
        Initialise le Coffre-fort avec une combinaison choisie.

        Paramètre :
            - une_combinaison, une liste de 4 entiers entre 0 et 99.

        Exemples :
        >>> #Test des cas invalides
        >>> coffre_test = CoffreFort( [ 0, 1, 2 ] )
        Traceback (most recent call last):
        AssertionError: Paramètre combinaison invalide.
        >>> coffre_test = CoffreFort( [ 0, 1, 2, 100 ] )
        Traceback (most recent call last):
        AssertionError: Paramètre combinaison invalide.
        >>> coffre_test = CoffreFort( [ -1, 1, 2, 3 ] )
        Traceback (most recent call last):
        AssertionError: Paramètre combinaison invalide.

        """
        self.set_combinaison( une_combinaison )

    def get_combinaison(self):
        """
        Accesseur de la combinaison.

        Retour : une liste de 4 entiers entre 0 et 99 inclusivement.

        Exemples :
        >>> coffre_test = CoffreFort( [ 0, 1, 2, 3 ] )
        >>> coffre_test.get_combinaison()
        [0, 1, 2, 3]
        """
        return self.__combinaison

    def set_combinaison(self, une_combinaison):
        """
        Mutateur de la combinaison

        Paramètre :
            - une_combinaison, une liste de 4 entiers entre 0 et 99.

        Exemples:
        >>> #Test un cas invalide
        >>> coffre_test = CoffreFort( [ 0, 1, 2, 3 ] )
        >>> coffre_test.set_combinaison( [ 7, 11, 13, 17 ])
        >>> coffre_test.get_combinaison()
        [7, 11, 13, 17]
        >>> #Test des cas invalides
        >>> coffre_test.set_combinaison( [ 0, 1, 2 ] )
        Traceback (most recent call last):
        AssertionError: Paramètre combinaison invalide.
        >>> coffre_test.set_combinaison( [ 0, 1, 2, 100 ] )
        Traceback (most recent call last):
        AssertionError: Paramètre combinaison invalide.
        >>> coffre_test.set_combinaison( [ 0, -1, 2, 3] )
        Traceback (most recent call last):
        AssertionError: Paramètre combinaison invalide.

        """',

'        #Votre code ici:

',
'
if __name__ == "__main__":
    import doctest
    doctest.testmod()
', "
import random
num_aléatoire=0
def numéroter(n):
    global num_aléatoire 
    numéros = [ $r, $s, $t, $u ]
    resultat = numéros[num_aléatoire]
    num_aléatoire+=1
    return resultat

random.randrange = numéroter","q4-gjtzelibcf.php");

?>
