class Compteur:
    """
    Un compteur du genre "distributeur de billets"

    Attributs : numéro_servi, un entier représentant le numéro présentement servi.

    """

    def __init__(self):
        """
        Initialise le premier numéro servi à 1.

        """
        #Pour que le premier numéro servi soit 1, on initialise à 0.
        self.numéro_servi=0

    def __str__(self):
        """
        Converti le Compteur en une chaîne de la forme «On sert le x» 
        où x est le numéro présentement servi.

        Retour : une chaîne de la forme «On sert le x»

        Exemples:
        >>> compteur_test = Compteur()
        >>> print(compteur_test)
        On sert le 0

        """
        pass

    def suivant(self):
        """
        Incrémente le numéro servi courant.

        Retour : le nouveau numéro servi.

        Exemples:
        >>> compteur_test = Compteur()
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

if __name__ == "__main__":
    import doctest
    doctest.testmod()

