<?php
namespace progression\domaine\entité\user;

enum État: string
{
	case INACTIF = "inactif";
	case ACTIF = "actif";
	case ATTENTE_DE_VALIDATION = "attente_de_validation";
}

?>
