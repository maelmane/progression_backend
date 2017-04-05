<?php

require('../quiz.php');


execute("Question 2", "
<br>
Votre classe Compteur pourrait être améliorée par l'ajout d'un constructeur paramétré. Ajoutez-lui un paramètre formel qui permette de choisir la valeur du premier numéro servi.
<br>
<br>
Vous pourrez trouver l'ébauche de la class <code>Compteur</code> ici : <a href='q2-nhziafccgo/compteur.py'>compteur.py</a>", "", "WqK4CAKF52", '
class Compteur:
    """
    Un compteur du genre "distributeur de billets" 

    Attributs : numéro_servi, un entier représentant le numéro présentement servi.

    """
 
    def __init__(self, un_numéro):
        """
        Initialise le premier numéro servi à <em>un_numéro</em>.

        Paramètre :
           - un_numéro : un entier positif ou nul réprésentant le premier numéro
                         devant être distribué par le Compteur.

        Exemples :
        #On essaie le cas erroné d\'un numéro initial négatif
        >>> Compteur(-1)
        Traceback (most recent call last):
        AssertionError: Paramètre un_numéro invalide.


        """
', '        pass','    def __str__(self):
        """
        Converti le Compteur en une chaîne de la forme «On sert le x» 
        où x est le numéro présentement servi.

        Retour : une chaîne de la forme «On sert le x»

        Exemples:
        >>> compteur_test = Compteur(1)
        >>> compteur_test.suivant()
        1
        >>> print(compteur_test)
        On sert le 1
        >>> compteur_test = Compteur(5)
        >>> compteur_test.suivant()
        5
        >>> print(compteur_test)
        On sert le 5

        """
        return "On sert le " + str(self.numéro_servi)

    def suivant(self):
        """
        Incrémente le numéro servi courant.

        Exemples:
        >>> compteur_test = Compteur(1)
        >>> print(compteur_test)
        On sert le 0
        >>> compteur_test.suivant()
        1
        >>> print(compteur_test)
        On sert le 1
        >>> print(compteur_test)
        On sert le 1
        >>> compteur_test.suivant()
        2
        >>> print(compteur_test)
        On sert le 2

        """
        self.numéro_servi+=1
        return self.numéro_servi

if __name__ == "__main__":
    import doctest
    doctest.testmod()
', "", "/quiz/quiz7/q3-wmomqpeksj.php");

?>
