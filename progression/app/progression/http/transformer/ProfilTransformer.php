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

namespace progression\http\transformer;

use progression\domaine\entité\user\{User, Occupation};
use progression\util\Encodage;
use League\Fractal\Resource\Collection;

class ProfilTransformer extends BaseTransformer
{
      public $type = "profile";

      public function transform(User $user)
      {

        $data = [
        "nom" => $user->nom,
        "prénom" => $user->prénom,
        "nom_complet" => $user->nom_complet,
        "pseudo" => $user->pseudo,
        "biographie" => $user->biographie,
        "occupation" => match ($user->occupation){
            Occupation::ETUDIANT => "étudiant",
            Occupation::ENSEIGNANT => "enseignant",
            Occupation::TUTEUR => "tuteur",
            Occupation::AUTRE => "utilisateur autre"
        },
        "avatar" => $user->avatar,
        ];

        return $data;
      }

}
