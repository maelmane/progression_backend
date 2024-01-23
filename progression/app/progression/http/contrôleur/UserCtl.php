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
use progression\http\transformer\UserTransformer;
use progression\http\transformer\dto\UserDTO;
use progression\domaine\entité\user\{User, État};
use progression\domaine\entité\Avancement;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\util\Encodage;
use DomainException;

class UserCtl extends Contrôleur
{
	public function get(Request $request, string $username): JsonResponse
	{
		Log::debug("UserCtl.get. Params : ", [$request->all(), $username]);

		$réponse = null;
		$user = $this->obtenir_user($username);
		$réponse = $this->valider_et_préparer_réponse($user, $user?->username);

		Log::debug("UserCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}

	/**
	 * @return array<string>
	 */
	public static function get_liens(string $username): array
	{
		$urlBase = Contrôleur::$urlBase;
		return [
			"self" => "{$urlBase}/user/{$username}",
			"avancements" => "{$urlBase}/user/{$username}/avancements",
			"clés" => "{$urlBase}/user/{$username}/cles",
			"tokens" => "{$urlBase}/user/{$username}/tokens",
		];
	}

	private function obtenir_user(string $username): User|null
	{
		Log::debug("UserCtl.obtenir_user. Params : ", [$username]);

		$userInt = new ObtenirUserInt();

		$user = $userInt->get_user(username: $username, includes: $this->get_includes());
		if ($user) {
			$user->avancements = $this->réencoder_uris($user->avancements);
		}

		Log::debug("UserCtl.obtenir_user. Retour : ", [$user]);
		return $user;
	}

	protected function valider_et_préparer_réponse($user, $id)
	{
		Log::debug("UserCtl.valider_et_préparer_réponse. Params : ", [$user]);

		if ($user) {
			$liens = self::get_liens($user->username);
			$dto = new UserDTO(id: $id, objet: $user, liens: $liens);

			$réponse = $this->item($dto, new UserTransformer());
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
