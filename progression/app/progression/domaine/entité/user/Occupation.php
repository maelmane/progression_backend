<?php

namespace progression\domaine\entité\user;

enum Occupation:string
{
    case AUTRE = "utilisateur autre";
    case ETUDIANT = "étudiant";
    case ENSEIGNANT = "enseignant";
    case TUTEUR = "tuteur";
}

?>
