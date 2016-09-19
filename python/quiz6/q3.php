<?php

require('../quiz.php');

$r=rand(0,999);
$s=rand(0,999);
$t=rand(0,999);
$u=rand(0,999);
$v=rand(0,999);

execute("Question 3", "Instanciez 5 Mogwais, placez-les dans une liste puis faites-les se présenter en <em>ordre inverse</em> de leur creation",
"Bonjour, je suis le Mogwai no $v
Bonjour, je suis le Mogwai no $u
Bonjour, je suis le Mogwai no $t
Bonjour, je suis le Mogwai no $s
Bonjour, je suis le Mogwai no $r", "KHpvmlEpt3", '

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

', "", "", "
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
