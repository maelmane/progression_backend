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

use progression\domaine\entité\user\{User, Occupation, État, Rôle};
use progression\http\transformer\ProfilTransformer;

class UserProfileCtl extends Contrôleur
{
    public function getProfile(string $username): string
    {

        $user = $this->getUserByUsername($username);


        if ($user === null) {
            return json_encode(["erreur" => "Profil non trouvé"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }


        $transformer = new ProfilTransformer();
        $transformedData = $transformer->transform($user);


        return json_encode($transformedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    private function getUserByUsername(string $username): ?User
    {

        switch ($username) {
            case 'martine':
                return new User(
                    'martine',
                    time(),
                    'MartineBouchard@gmail.com',
                    État::ACTIF,
                    Rôle::ADMIN,
                    ['avancement1', 'avancement2'],
                    ['clé1' => 'valeur1', 'clé2' => 'valeur2'],
                    'Préférences utilisateur 1',
                    'Bouchard',
                    'Martine',
                    'Martine Phillipa Bouchard',
                    'Didine',
                    'Enseignante depuis toujours, je suis passionée de programmation orientée objet',
                    Occupation::ENSEIGNANT,
                    'https://thumbs.dreamstime.com/z/avatar-de-programmeur-langue-logiciel-110589785.jpg'
                );
            case 'david':
                return new User(
                    'david',
                    time(),
                    'DavidLefevre@gmail.com',
                    État::ACTIF,
                    Rôle::NORMAL,
                    ['avancement3', 'avancement4'],
                    ['clé3' => 'valeur3', 'clé4' => 'valeur4'],
                    'Préférences utilisateur 2',
                    'Lefevre',
                    'David',
                    'David Lefevre',
                    'DavLev',
                    'Développeur passionné par les nouvelles technologies et les applications web.',
                    Occupation::PROGRAMMEUR,
                    'lien_de_l_avatar_de_david.jpg'
                );
            case 'simon':
                return new User(
                    'simon',
                    time(),
                    'SimonTremblay@gmail.com',
                    État::ACTIF,
                    Rôle::NORMAL,
                    ['avancement5', 'avancement6'],
                    ['clé5' => 'valeur5', 'clé6' => 'valeur6'],
                    'Préférences utilisateur 3',
                    'Tremblay',
                    'Simon',
                    'Simon Tremblay',
                    'SimTrem',
                    'Étudiant en informatique avec une passion pour l\'intelligence artificielle.',
                    Occupation::ÉTUDIANT,
                    'lien_de_l_avatar_de_simon.jpg'
                );
            case 'judy':
                return new User(
                    'judy',
                    time(),
                    'JudyLavoie@gmail.com',
                    État::ACTIF,
                    Rôle::NORMAL,
                    ['avancement7', 'avancement8'],
                    ['clé7' => 'valeur7', 'clé8' => 'valeur8'],
                    'Préférences utilisateur 4',
                    'Lavoie',
                    'Judy',
                    'Judy Lavoie',
                    'JudLav',
                    'Étudiante dévouée à l\'éducation des jeunes générations, spécialisée en sciences informatiques.',
                    Occupation::TUTEUR,
                    'lien_de_l_avatar_de_judy.jpg'
                );
            // Si quelqu'un veut ajouter d'autres users pour tests
            default:
                return null; // Retourne null si le nom d'utilisateur n'est pas trouvé
        }
    }
}
