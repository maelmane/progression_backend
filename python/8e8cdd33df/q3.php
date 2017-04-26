<?php

require('../quiz.php');


$r=rand(1000,9999);

file_put_contents('/opt/pyjail/tmp/fichier_test.txt','Secret : '.$r."\nCe fichier ne contient rien d'autre.");

$s=rand(1000,9999);

file_put_contents('/opt/pyjail/tmp/fichier_inaccessible_'.$s.'.txt','Ce fichier ne contient rien d\'autre.');
chmod('/opt/pyjail/tmp/fichier_inaccessible_'.$s.'.txt',0);

execute("Question 3", "Un fichier inexistant n'est pas le seul problème qui puisse survenir. Le fichier demandé peut aussi être inaccessible à l\'utilisateur. Faites afficher un message adéquat dans les deux cas. Ho... en passant, l'Exception lancée lorsqu'un fichier ne peut être ouvert possède un attribut <code>errno</code>.", "", "SJxbvtagzO", '

def cat(nom_fichier):
    """
    Lit et affiche le contenu d\'un fichier

    Paramètre :
    - nom_fichier : Le nom complet du fichier à lire

    Exemples :
    >>> #Lit et affiche le contenu du fichier
    >>> cat("/tmp/fichier_test.txt")
    Secret : '. $r . '
    Ce fichier ne contient rien d\'autre.

    >>> #Tente de lire un fichier inexistant
    >>> cat("/tmp/fichier_inexistant_'. $s .'.txt")
    Erreur: Le fichier /tmp/fichier_inexistant_'. $s .'.txt n\'existe pas.

    >>> #Tente de lire un fichier inaccessible en lecture
    >>> cat("/tmp/fichier_inaccessible_'. $s .'.txt")
    Erreur: Le fichier /tmp/fichier_inaccessible_'. $s .'.txt n\'est pas accessible en lecture.

    """',
    '','# --- Exécute les tests ---
if __name__ == "__main__":
    import doctest
    doctest.testmod()', 'f_temp=open("/tmp/fichier_test.txt","w");f_temp.write("Secret : '.$r.'\nCe fichier ne contient rien d\'autre.\n");f_temp.close();f_temp=open("/tmp/fichier_inaccessible_'.$s.'.txt","w");import os;os.chmod("/tmp/fichier_inaccessible_'.$s.'.txt",0);', "q4.php");

?>
