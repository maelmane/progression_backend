<?php

require('../quiz.php');

$r=rand(0,99);
$s=rand(0,99);
$t=rand(0,99);
$u=rand(0,99);

execute("Question 4", "Ajoutons une porte à notre coffre-fort et deux méthodes, <code>ouvrir</code> et <code>fermer</code>. <code>ouvrir</code> prend une combinaison en paramètre et n'ouvre la porte que si celle-ci correspond à la combinaison du coffre. <code>fermer</code>, quant à elle, ferme la porte de façon inconditionnelle. Une troisième méthode, <code>est_ouvert</code> retourne Vrai si et seulement si la porte est ouverte. Vous aurez besoin d'un attribut supplémentaire : <code>__état_porte</code>
<br><br>
<img src='uml.jpg'>
", "Le coffre-fort est ouvert\nLe coffre-fort est inviolable!\nLe coffre-fort est inviolable!\nLe coffre-fort est ouvert\nLe coffre-fort est ouvert", "clé1", '
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
        self.__état_porte = True
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

        """
        #Vérifie le nombre de chiffres dans la combinaison
        assert len(une_combinaison) == 4, "Paramètre combinaison invalide."
        #Vérifie que chaque chiffre est entre 0 et 99 inclusivement.
        for i in range(4):
            assert une_combinaison[i] in range(100), "Paramètre combinaison invalide."

        self.__combinaison = une_combinaison',

'    #Votre code ici:

',
'
def vérifier_porte():
    if mon_coffre.est_ouvert():
        print("Le coffre-fort est ouvert")
    else:
        print("Le coffre-fort est inviolable!")


#Programme principal
mon_coffre = CoffreFort( [' . "$r, $s, $t, $u" .  '] )
vérifier_porte()
mon_coffre.fermer()
vérifier_porte()
mon_coffre.ouvrir( [0, 1, 2, 3] )
vérifier_porte()
mon_coffre.ouvrir( ['. "$r, $s, $t, $u" . '] )
vérifier_porte()
mon_coffre.ouvrir( [0, 1, 2, 3] )
vérifier_porte()

if __name__ == "__main__":
    import doctest
    doctest.testmod()
', "
import random
num_aléatoire=0
def numéroter(n):
    global num_aléatoire 
    numéros = [ $r, $s, $t, $u, $v ]
    resultat = numéros[num_aléatoire]
    num_aléatoire+=1
    return resultat

random.randrange = numéroter");

?>
