<?php

require('../quiz.php');

execute("Question 1", "Complétez les tests de la classe Dé", "", "JCmIVdPY4N", '
import random

class  Dé:
    """
    Un dé à jouer d\'un nombre variable de faces.
    
    """
    def __init__(self, faces):
        """
        Initialise le dé.

        Paramètre:
        - faces : entier, le nombre de faces du dé.

        Exemples :','        >>> ', '
        >>> dé_test.face
        1

        """
        assert faces > 1, "Le nombre de faces doit être > 1"

        #Le nombre de faces du dé
        self.__faces = faces

        #Le nombre de points sur la face supérieure
        self.__face = 1

    @property
    def face(self):
        """
        Propriété en lecture seule de la face supérieure du dé.

        Retour : un entier représentant le nombre de points sur la face supérieure du dé.

        """
        return self.__face

# --- Exécute les tests ---
if __name__ == "__main__":
    import doctest
    doctest.testmod()
','', "q2.php");

?>
