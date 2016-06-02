<?php

require('../quiz.php');

$r=rand(0,999);
$rp=$r+1;
$s=rand(0,999);
$t=rand(0,999);
$u=rand(0,999);
$v=rand(0,999);

execute("Question 4", "Lorsque les Mogwais sont mouillés, ils se divisent et créent un nouveau clone d'eux-même. La seule différence est que le nouveau Mogwai porte un numéro incrémenté de 1 par rapport à son parent. Implémentez la méthode <code>mouiller</code> qui permet de créer le nouvel objet. ",
"Bonjour, je suis le Mogwai no $r
Bonjour, je suis le Mogwai no $rp" , "jdP7QDy4O9", '

import random

class Mogwai:
    """
    Un Mogwai, être attachant et sans malice.

    Attributs : numéro, un entier entre 0 et 999 identifiant chaque mogwai.

    """
    def présenter(self):
        """
        Presente un Mogwai
    
        """
        print("Bonjour, je suis le Mogwai no " + str(self.numéro))

    def __init__(self):
        """
        Initialise le Mogwai.

        """
        self.numéro = random.randrange(1000)

    def mouiller(self):
        """
        Retourne un nouveau Mogwai clone du premier.

        Instancie un nouveau Mogwai avec un numéro incrémenté de 1 par rapport à son parent.

        Retour : Un nouvel objet Mogwai.

        """
', "        return None", "

guizmo = Mogwai()
clone = guizmo.mouiller()

guizmo.présenter()
clone.présenter()

", "
import random
num_aléatoire=0; 
def numéroter(n):
    global num_aléatoire 
    numéros = [ $r, $s, $t, $u, $v ]
    resultat = numéros[num_aléatoire]
    num_aléatoire+=1
    return resultat

random.randrange = numéroter");

?>
