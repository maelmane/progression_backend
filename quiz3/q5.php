<?php

require('../quiz.php');

$r=rand(0,1000);
$s=2*$r;
execute("Question 5", "Exécutez la fonction <code>multiplier</code> pour faire afficher le double de <code>nombre</code>.", $s, "TNAXMaU76c", "
def multiplier(multiplicateur):
    \"\"\"
    Affiche un multiple d'un nombre entier «magique».
    
    Affiche un nombre entier «magique» multiplié par un multiplicateur fourni en paramètre.

    Paramètres:
    multiplicateur : nombre entier multiplicateur du nombre magique.

    \"\"\"
    nombre = $r
    print(multiplicateur * nombre)

"
 
);

?>
