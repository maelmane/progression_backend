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

use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use progression\http\transformer\UserTransformer;
use progression\domaine\entité\Avancement;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\domaine\interacteur\SauvegarderPréférencesUtilisateurInt;
use progression\util\Encodage;

class UserCtl extends Contrôleur
{
	public function get(Request $request, $username = null)
	{
		Log::debug("UserCtl.get. Params : ", [$request->all(), $username]);

		$user = $this->obtenir_user($username);

		$réponse = $this->valider_et_préparer_réponse($user);
		Log::debug("UserCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}

	public function post(Request $request, string $username): JsonResponse
	{
		Log::debug("UserCtl.post. Params : ", [$request->all(), $username]);
		$validation = $this->valider_paramètres($request);

		if ($validation->fails()) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ") Paramètres invalides",
			);
			return $this->réponse_json(["erreur" => $validation->errors()], 400);
		}

		$userInt = new SauvegarderPréférencesUtilisateurInt();
		$user = $userInt->sauvegarder_préférences($username, $request->préférences ?? "");

		$réponse = $this->valider_et_préparer_réponse($user);

		Log::debug("UserCtl.post. Retour : ", [$réponse]);
		return $réponse;
	}

	private function obtenir_user($username)
	{
		Log::debug("UserCtl.obtenir_user. Params : ", [$username]);

		$userInt = new ObtenirUserInt();
		$user = null;

		if ($username != null && $username != "") {
			$user = $userInt->get_user($username, $this->get_includes());
			if ($user) {
				$user->avancements = $this->réencoder_uris($user->avancements);
			}
		}

		Log::debug("UserCtl.obtenir_user. Retour : ", [$user]);
		return $user;
	}

	private function valider_paramètres(Request $request)
	{
		$validateur = Validator::make(
			$request->all(),
			[
				"préférences" => "string|json|between:0,65535",
			],
			[
				"json" => "Err: 1003. Le champ :attribute doit être en format json.",
				"paramètres.between" =>
					"Err: 1002. Le champ :attribute " . mb_strlen($request->paramètres) . " > :max caractères.",
			],
		);

		return $validateur;
	}

	private function valider_et_préparer_réponse($user)
	{
		Log::debug("UserCtl.valider_et_préparer_réponse. Params : ", [$user]);

		if ($user) {
			$user->id = $user->username;
			$réponse = $this->item($user, new UserTransformer());
		} else {
			$réponse = null;
		}

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("UserCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);

		return $réponse;
	}

	/**
	 * @param array<Avancement> $avancements
	 * @return array<Avancement>
	 */
	private function réencoder_uris(array $avancements): array
	{
		$avancements_réencodés = [];

		foreach ($avancements as $uri => $avancement) {
			$avancements_réencodés[Encodage::base64_encode_url($uri)] = $avancement;
		}

		return $avancements_réencodés;
	}
}
