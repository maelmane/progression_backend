<?php
/*
   This file is part of Progression.

   Progression is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Progression is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Progression.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace progression\http\contrôleur;

//Contrôleur des profiles utilisateur
class UserProfileCtl extends Contrôleur
{
    public function getProfile(string $username): String
    {
        // Profils factices pour les tests Front-End
        $profils = [
            "martine" => [
                "nom" => "Bouchard",
                "prénom" => "Martine",
                "nom_complet" => "Martine Bouchard",
                "pseudo" => "Didine",
                "biographie" => "Passionnée de la programmation bas-niveau, j'ai trouvé un réel intérêt pour les systèmes d'exploitation",
                "occupation" => "enseignant",
                "avatar" => "https://static.vecteezy.com/ti/vecteur-libre/p1/5878308-programmeur-travaillant-moderne-concept-plat-pour-la-conception-de-bannieres-web-femme-developpeur-travaille-sur-ordinateur-portable-et-programmes-en-php-et-autres-langages-de-programmation-illustrationle-avec-scene-de-personnes-isolees-vectoriel.jpg"
            ],
            "david" => [
                "nom" => "Lefevre",
                "prénom" => "David",
                "nom_complet" => "David Lefevre",
                "pseudo" => "DavLev",
                "biographie" => "Développeur passionné par les nouvelles technologies et les applications web.",
                "occupation" => "programmeur",
                "avatar" => "lien_de_l_avatar_de_david.jpg"
            ],
            "simon" => [
                "nom" => "Tremblay",
                "prénom" => "Simon",
                "nom_complet" => "Simon Tremblay",
                "pseudo" => "SimTrem",
                "biographie" => "Étudiant en informatique avec une passion pour l'intelligence artificielle.",
                "occupation" => "étudiant",
                "avatar" => "lien_de_l_avatar_de_simon.jpg"
            ],
            "judy" => [
                "nom" => "Lavoie",
                "prénom" => "Judy",
                "nom_complet" => "Judy Lavoie",
                "pseudo" => "JudLav",
                "biographie" => "Étudiante dévouée à l'éducation des jeunes générations, spécialisée en sciences informatiques.",
                "occupation" => "tuteur",
                "avatar" => "lien_de_l_avatar_de_judy.jpg"
            ]
        ];

        // Retour du bon profil, cette fonction changera pour être remplacée par l'appel à l'interacteur..
        if (isset($profils[$username])) {
            return json_encode($profils[$username], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            return json_encode(["erreur" => "Profil non trouvé"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
}
