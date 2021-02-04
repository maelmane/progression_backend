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

namespace progression\http\controleurs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use progression\domaine\interacteurs\AuthentificationInteracteur;

class LoginControleur extends Controller
{
    function __construct()
    {
    }

    public function login(Request $request){
        $authInt = new AuthentificationInteracteur();
        $nomUtilisateur = $request->input("username");
        $token = $authInt->créerToken($nomUtilisateur);

        if($token){
            Log::info("Le token a été créé pour: " . $request->ip() . " (LoginInteracteur)");
            return response()->json(['token' => $token], 200);
        }
        Log::warning("Le token n'a pas été créé pour: " . $request->ip() . " (LoginInteracteur)");
        return response()->json(['message' => 'Utilisateur non autorisé.'], 401);
    }
}

?>