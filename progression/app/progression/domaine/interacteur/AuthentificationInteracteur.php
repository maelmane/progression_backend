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
?><?php

namespace progression\domaine\interacteurs;

use \Firebase\JWT\JWT;
use progression\domaine\interacteurs\LoginInteracteur;

class AuthentificationInteracteur
{
    function __construct()
    {
    }

    public function créerToken($nomUtilisateur){
        $loginInt = new LoginInteracteur();
        $objetUser = $loginInt->login($nomUtilisateur);

        if($objetUser!=null){
            $payload = [
                'user' => $objetUser,
                'current' => time(),
                'expired' => time() + env("JWT_TTL")
            ];
            try {
                $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
                return $token;
            } catch(\Exception $e) {
                return null;
            }
        }
        return $objetUser;
    }
}

?>
