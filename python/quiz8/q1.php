<?php

require('../quiz.php');

$r=rand(0,99);
$s=rand(0,99);
$t=rand(0,99);
$u=rand(0,99);

execute("Question 1", "Faites afficher la combinaison du CoffreFort mon_coffre sous forme de liste.", "[$r, $s, $t, $u]", "clé1", '
import random

class CoffreFort:
    """
    Un coffre-fort très peu sécuritaire.

    Attributs : combinaison, une liste de 4 chiffres entre 0 et 99.

    """
    def __init__(self, une_combinaison):
        """
        Initialise le Coffre-fort avec une combinaison choisie.

        Paramètre :
            - une_combinaison, une liste de 4 entiers entre 0 et 99.

        """
        self.combinaison = une_combinaison


mon_coffre = CoffreFort( [' . "$r, $s, $t, $u" .  '] )', 
" #Votre code ici:
print(42)", "", "
import random
num_aléatoire=0
def numéroter(n):
    global num_aléatoire 
    numéros = [ $r, $s, $t, $u ]
    resultat = numéros[num_aléatoire]
    num_aléatoire+=1
    return resultat

random.randrange = numéroter","q2-yzugrpwnhk.php");

?>
