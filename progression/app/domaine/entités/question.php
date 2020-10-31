<?php

require_once __DIR__ . '/entite.php';
require_once __DIR__ . '/avancement.php';

class Question extends Entite
{
    const ETAT_CACHE = -1;
    const ETAT_DEBUT = 0;
    const ETAT_NONREUSSI = 1;
    const ETAT_REUSSI = 2;

    const TYPE_PROG_EVAL = 0;
    const TYPE_SYS = 1;
    const TYPE_PROG_MULTITEST = 2;

    public $serieID;
    public $actif;
    public $numero;
    public $titre;
    public $description;
    public $enonce;
    public $etat;
    public $code_validation;
    public $avancement;
}

?>
