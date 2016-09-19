<?php

require('../quiz.php');

$r=rand(0,99);
$s=rand(0,99);
$t=rand(0,99);
$u=rand(0,99);

execute("Question 2", "L'attribut combinaison est maintenant privé. Procurez-lui un accesseur <code>get_combinaison</code>.", "[$r, $s, $t, $u]", "clé1", '
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

        """
        self.__combinaison = une_combinaison',
'    #Votre code ici:

',
'
mon_coffre = CoffreFort( [' . "$r, $s, $t, $u" .  '] )
print(mon_coffre.get_combinaison())

', "
import random
num_aléatoire=0
def numéroter(n):
    global num_aléatoire 
    numéros = [ $r, $s, $t, $u ]
    resultat = numéros[num_aléatoire]
    num_aléatoire+=1
    return resultat

random.randrange = numéroter","q3-zfmblcuupi.php");

?>
