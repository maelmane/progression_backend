<?php

require('../quiz.php');


execute("Question 3", "
<br>
Pour être utile, le compteur doit aussi pouvoir distribuer des billets aux clients. Puisque les numéros servis et distribués avancent rarement à la même vitesse, on doit ajouter un attribut <code>numéro_distribué</code> ainsi qu'une méthode <code>distribuer</code> retournant le numéro du billet distribué.
<br>
<br>
Vous pourrez trouver l'ébauche de la class <code>Compteur</code> ici : <a href='q3-wmomqpeksj/compteur.py'>compteur.py</a>", "", "WqK4CAKF52", '
class Compteur:
    """
    Un compteur du genre "distributeur de billets" 

    Attributs : 
        - numéro_servi, un entier représentant le numéro présentement servi.
        - numéro_distribué, un entier tenant le compte du dernier "billet" distribué.

    """
 
    def __init__(self, un_numéro):
        """
        Initialise le compte à <em>un_numéro</em>.

        Paramètre :
           - un_numéro : un entier réprésentant le premier numéro 
                         distribué par le Compteur.

        Exemples :
        #On essaie le cas erroné d\'un numéro initial négatif
        >>> Compteur(-1)
        Traceback (most recent call last):
        AssertionError: Paramètre un_numéro invalide.

        """
','
        #On s\'assure que le paramètre est non négatif.
        assert un_numéro >=0, "Paramètre un_numéro invalide."
        
        #Pour que le premier numéro servi soit 1, on initialise à un de moins.
        self.numéro_servi = un_numéro-1
        
    def __str__(self):
        """
        Converti le Compteur en une chaîne de la forme «On sert le x» 
        où x est le dernier numéro distribué.

        Retour : une chaîne de la forme «On sert le x»
        Exemples:
        >>> compteur_test = Compteur(1)
        >>> compteur_test.distribuer()
        1
        >>> compteur_test.suivant()
        1
        >>> print(compteur_test)
        On sert le 1
        >>> compteur_test = Compteur(5)
        >>> compteur_test.distribuer()
        5
        >>> compteur_test.suivant()
        5
        >>> print(compteur_test)
        On sert le 5

        """
        return "On sert le " + str(self.numéro_servi)

    def suivant(self):
        """
        Incrémente le numéro servi courant.

        Le numéro servi ne peut jamais être plus grand que le dernier billet distribué.

        Exemples:
        >>> compteur_test = Compteur(1)
        >>> print(compteur_test)
        On sert le 0

        #Aucun billet n\'a encore été distribué.
        >>> compteur_test.suivant()
        0
        >>> print(compteur_test)
        On sert le 0
        >>> compteur_test.distribuer()
        1
        >>> print(compteur_test)
        On sert le 0
        >>> compteur_test.suivant()
        1
        >>> print(compteur_test)
        On sert le 1

        """
        self.numéro_servi+=1
        return self.numéro_servi
        
    def distribuer(self):
        """
        Distribue un billet.

        Le billet distribué est un numéro entier incrémenté de 1 à chaque appel.

        Retour : un nombre entier, numéro de billet distribué.

        Exemples:
        >>> compteur_test = Compteur(1)
        >>> compteur_test.distribuer()
        1
        >>> compteur_test.distribuer()
        2
        >>> compteur_test.suivant()
        1
        >>> print(compteur_test)
        On sert le 1
        >>> compteur_test.suivant()
        2
        >>> print(compteur_test)
        On sert le 2

        """
        pass
','
if __name__ == "__main__":
    import doctest
    doctest.testmod()
', "", "index.html");

?>
