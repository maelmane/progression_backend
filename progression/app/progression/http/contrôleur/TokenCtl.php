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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use progression\domaine\entité\{User};

class TokenCtl extends Contrôleur
{
    public function post(Request $request, $username)
    {
        $ressources = $request->input("ressources");
        $expiration = time() + strtotime("+ 2 years");
        $user = new User($username);

        $token = GénérateurDeToken::get_instance()->générer_token($user, $ressources, $expiration);
        $réponse = $this->préparer_réponse(["Token" => $token]);
        Log::debug("TokenCtl.post. Réponse : ", [$réponse]);

        return $réponse;
    }
}
