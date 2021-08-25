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

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use progression\domaine\interacteur\LoginInt;

class LoginCtl extends Contrôleur
{
	
	public function login(Request $request)
	{
		$user = null;
		$token = null;

		$validation = $this->validerParams($request);
		if ($validation->fails()) {
			return $this->réponse_json(["erreur" => $validation->errors()], 422);
		}

		$username = $request->input("username");
		$password = $request->input("password");

		$loginInt = new LoginInt();
		$user = $loginInt->effectuer_login($username, $password);

		if ($user != null) {
			$token = GénérateurDeToken::get_instance()->générer_token($user);
			return $this->préparer_réponse(["Token" => $token]);
		}
		else{
			Log::warning(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
				get_class($this) .
				") Accès interdit. username: $username",
			);
			return $this->réponse_json(["erreur" => "Accès interdit."], 401);
		}
	}

	private function validerParams($request)
	{
		return Validator::make(
			$request->all(),
			[
				"username" => "required|alpha_dash",
				"password" => "required",
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
			],
		);
	}
}
