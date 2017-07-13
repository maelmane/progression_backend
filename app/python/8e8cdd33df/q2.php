<?php

require('../quiz.php');


$r=rand(1000,9999);

file_put_contents('/opt/pyjail/tmp/fichier_test.txt','Secret : '.$r."\nCe fichier ne contient rien d'autre.");

$s=rand(1000,9999);

execute("Question 2", "Ça fonctionne lorsque le fichier existe... mais sinon, le programme s'arrête avec un <em>stacktrace</em>. Il serait bien mieux d'afficher un message plus informatif, du genre «Erreur: Le fichier <em>nom de fichier</em> n'existe pas.».", "", "NrlGiS6BOs", '

def cat(nom_fichier):
    """
    Lit et affiche le contenu d\'un fichier

    Paramètre :
    - nom_fichier : Le nom complet du fichier à lire

    Exemples :
    >>> #Lit et affiche le contenu du fichier
    >>> cat("/tmp/fichier_test.txt")
    Secret : '.$r.'
    Ce fichier ne contient rien d\'autre.

    >>> #Tente de lire un fichier inexistant
    >>> cat("fichier_inexistant_'. $s .'.txt")
    Erreur: Le fichier fichier_inexistant_'. $s .'.txt n\'existe pas.

    """',
    '','# --- Exécute les tests ---
if __name__ == "__main__":
    import doctest
    doctest.testmod()', '', "q3.php");

?>
