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

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use progression\domaine\interacteur\AuthentificationInteracteur;

class LoginCtl extends Contrôleur
{
    public function login(Request $request){
        $authInt = new AuthentificationInteracteur();
        $username = $request->input("username");
        $password = $request->input("password");

        $token = $authInt->créerToken($username, $password);
        if($token){
            Log::info("Le token a été créé pour: " . $request->ip() . " (LoginInteracteur)");
            return $this->réponseJson(['token' => $token], 200);
        }
        Log::warning("Le token n'a pas été créé pour: " . $request->ip() . " (LoginInteracteur)");
        return $this->réponseJson(['message' => 'Utilisateur non autorisé.'], 401);
    }
}

?>
