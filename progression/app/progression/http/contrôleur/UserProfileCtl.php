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
use progression\domaine\interacteur\ObtenirUserInt;

class UserProfileCtl extends Contrôleur
{

    public function getUser(string $username): User
    {        
        $userInt = new ObtenirUserInt();

        $user = $userInt->get_user(username: $username, includes: $this->get_includes());
        return $user;
    }


    public function getProfile(string $username): string
    {

        $user = $this->getUser($username);


        if ($user === null) {
            return json_encode(["erreur" => "Profil non trouvé"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }


        $transformer = new ProfilTransformer();
        $transformedData = $transformer->transform($user);


        return json_encode($transformedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


 }

