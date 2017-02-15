<?php

require('../quiz.php');

$r=rand(0,999);
$s=rand(0,999);
$t=rand(0,999);
$u=rand(0,999);
$v=rand(0,999);

execute("Question 2", "Instanciez deux Mogwais et faites-les se présenter grâce à la methode <code>présenter</code>", "Bonjour, je suis le Mogwai no $r\nBonjour, je suis le Mogwai no $s", "WMlvsN17j5", '

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
