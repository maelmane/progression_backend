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
 
 class ModifierProfilTransformer extends BaseTransformer
 {
     public $type = "profile";
 
     protected $user;
 
     public function __construct(User $user)
     {
         $this->user = $user;
     }
 
     public function transformInput(array $input)
     {
        $data = [
            "nom" => isset($input['nom']) ? htmlentities($input['nom']) : htmlentities($this->user->nom),
            "prénom" => isset($input['prénom']) ? htmlentities($input['prénom']) : htmlentities($this->user->prénom),
            "nom_complet" => isset($input['nom_complet']) ? htmlentities($input['nom_complet']) : htmlentities($this->user->nom_complet),
            "pseudo" => isset($input['pseudo']) ? htmlentities($input['pseudo']) : htmlentities($this->user->pseudo),
            "biographie" => isset($input['biographie']) ? htmlentities($input['biographie']) : htmlentities($this->user->biographie),
            "avatar" => isset($input['avatar']) ? filter_var($input['avatar'], FILTER_SANITIZE_URL) : $this->user->avatar,
        ];
        
        return $data;
    }

}
 
