<?php

namespace progression\domaine\entité\user;

enum Occupation:string
{
    case ETUDIANT = "étudiant";
    case ENSEIGNANT = "enseignant";
    case TUTEUR = "tuteur";
    case AUTRE = "utilisateur autre";
}

?>
