<?php
namespace progression\dao;

use progression\domaine\entité\user\Profil;

$profil1 = new Profil(
    "john.doe@gmail.com",
    "John Doe Junior",
    "John",
    "Doe"
);

$profil2 = new Profil(
    "jane.smith@outlook.ca",
    "Jane Henriette Smith",
    "Jane",
    "Smith"
);
$profil3 = new Profil(
    "bob.jones@yahoo.com",
    "Bob Johnson",
    "Bob",
    "Johnson"
);

$profil4 = new Profil(
    "alice.white@gmail.com",
    "Alice Marie White",
    "Alice",
    "White"
);

$profil5 = new Profil(
    "sam.brown@hotmail.com",
    "Samuel Brown",
    "Sam",
    "Brown"
);



$profils = [$profil1, $profil2, $profil3, $profil4, $profil5];
return $profils;
