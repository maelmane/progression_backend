<?php

require('../quiz.php');

$r=rand(0,999);
$s=rand(0,999);
$t=rand(0,999);
$u=rand(0,999);
$v=rand(0,999);

execute("Question 5", "Lorsqu'on donne à manger à un Mogwai, il se transforme en bête féroce appelée un Gremlin. Implémentez la méthode manger qui retourne un nouvel objet Gremlin portant le même numéro que le Mogwai si l'heure passée est entre minuit et 7h. Implémentez aussi la classe Gremlin qui se présente en affichant «Grrr! je suis le Gremlin no xxx» où xxx est son numéro de Gremlin.", 
"None
Grrr! je suis le Gremlin no $r", "EjNinHRJDv", '

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

    def manger(self, heure, minutes):
        """
        Fait manger le Mogwai à une certaine heure.

        Paramètres : heure, un entier représentant l\'heure à laquelle le Mogwai doit manger.
                     minutes, un entier représentant les minutes de l\'heure à laquelle le Mogwai doit manger.

        Retour : Un nouvel objet Gremlin si le Mogwai mange entre 00:00 et 07:00 ou None sinon. 

        """
        if heure>0 and heure<7:
            clone = Gremlin()
            clone.numéro = self.numéro
            return clone
        else :
            return None

class Gremlin:
    """
    Un Gremlin, monstre affamé mais divertissant.
   
    Attributs :
    numéro : un entier identifiant le Gremlin.

    """
    #À compléter
 
', "", "

#Crée un nouveau Mogwai
guizmo = Mogwai()

#Donne à manger au Mogwai à 9h15 (pas de problème).
gremo = guizmo.manger( 9, 15 )
print(gremo)

#Donne à manger au Mogwai à 4h15 (ho ho...).
gremo = guizmo.manger( 4, 15 )

#Si gremo est un objet Gremlin, on le fait se présenter.
if isinstance(gremo, Gremlin):
    gremo.présenter()

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
