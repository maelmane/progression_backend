<?php

require('../quiz.php');

$r=rand(0,999);

execute("Question 1", "Instanciez un Mogwai et faites-le se présenter grâce à la methode <code>présenter</code>.", "Bonjour, je suis le Mogwai no $r", "kroYyJ2zLS", '
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
        
', "", "", "import random; random.randrange = lambda x: $r");

?>
