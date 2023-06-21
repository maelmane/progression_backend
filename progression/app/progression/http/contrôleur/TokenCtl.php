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
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator as ValidatorImpl;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use progression\http\transformer\TokenTransformer;

class TokenCtl extends Contrôleur
{
	public function post(Request $request, string $username): JsonResponse
	{
		Log::debug("TokenCtl.post. Params : ", [$request->all()]);

		$validateur = $this->valider_paramètres($request);

		if ($validateur->stopOnFirstFailure()->fails()) {
			$réponse = $this->réponse_json(["erreur" => $validateur->errors()], 400);
		} else {
			$data = $request->input("data");
			$ressources = $data["ressources"];
			$expiration = $data["expiration"];

			$token = GénérateurDeToken::get_instance()->générer_token(
				username: $username,
				expiration: $expiration,
				ressources: $ressources ?? ["tout" => ["url" => ".*", "method" => ".*"]],
			);
			$réponse = $this->préparer_réponse($this->item($token, new TokenTransformer($username)));
		}

		Log::debug("TokenCtl.post. Réponse : ", [$réponse]);
		return $réponse;
	}

	private function valider_paramètres(Request $request): ValidatorImpl
	{
		$validateur = Validator::make(
			$request->all(),
			[
				"data" => "required",
				"data.ressources" => "required",
				"data.ressources.*.url" => "required|string",
				"data.ressources.*.method" => "required|string",
				"data.expiration" => "required|integer",
			],
			[
				"required" => "Err: 1004. Le champ :attribute est obligatoire.",
				"data.expiration.integer" => "Err: 1003. Le champ expiration doit être un nombre entier.",
			],
		);

		return $validateur;
	}
}
