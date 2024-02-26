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


class UserProfileCtl extends Contrôleur
{
    public function getProfile(string $username): String
    {
        $resultat = [
            "nom" => "Bouchard",
            "prénom" => "Martine",
            "nom_complet" => "Martine Bouchard",
            "pseudo" => "Didine",
            "biographie" => "Passionnée de la programmation bas-niveau, j'ai trouvé un réel intérêt pour les systèmes d'exploitation",
            "occupation" => "enseignant",
            "avatar" => "https://static.vecteezy.com/ti/vecteur-libre/p1/5878308-programmeur-travaillant-moderne-concept-plat-pour-la-conception-de-bannieres-web-femme-developpeur-travaille-sur-ordinateur-portable-et-programmes-en-php-et-autres-langages-de-programmation-illustrationle-avec-scene-de-personnes-isolees-vectoriel.jpg"
        ];

        return json_encode($resultat, JSON_PRETTY_PRINT);
    }
}
