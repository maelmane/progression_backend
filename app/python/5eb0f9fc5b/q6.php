<?php

require('../quiz.php');

$r=rand(0,1000);
$s=2*$r;
execute("Question 6", "La fonction <code>multiplier</code> a été modifiée. Désormais, elle <em>retourne</em> un nombre entier. Utilisez la fonction <code>multiplier</code> pour faire afficher le double de <code>nombre</code>.", $s, "3FdPJ0oz1B", "
def multiplier(multiplicateur):
    \"\"\"
    Affiche un multiple d'un nombre entier «magique».
    
    Affiche un nombre entier «magique» multiplié par un multiplicateur fourni en paramètre.

    Paramètres:
    multiplicateur : nombre entier multiplicateur du nombre magique.

    Retourne: un entier multiple du nombre magique.

    \"\"\"
    nombre = $r
    return multiplicateur * nombre

"
 
);

?>
