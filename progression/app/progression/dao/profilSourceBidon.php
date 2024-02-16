<?php
namespace progression\dao;

use progression\domaine\entité\user\Profil;

$profil1 = new Profil(
    "John Doe Junior",
    "John",
    "Doe"
);

$profil2 = new Profil(
    "Jane Henriette Smith",
    "Jane",
    "Smith"
);
$profil3 = new Profil(
    "Bob Johnson",
    "Bob",
    "Johnson"
);

$profil4 = new Profil(
    "Alice Marie White",
    "Alice",
    "White"
);

$profil5 = new Profil(
    "Samuel Brown",
    "Sam",
    "Brown"
);



$profils = [$profil1, $profil2, $profil3, $profil4, $profil5];
echo $profils;
return $profils;
