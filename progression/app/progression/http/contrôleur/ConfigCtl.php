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
use Illuminate\Support\Facades\Log;
use progression\domaine\entité\user\User;
use progression\http\transformer\ConfigTransformer;
use progression\http\transformer\dto\GénériqueDTO;

class ConfigCtl extends Contrôleur
{
	public function get(Request $request)
	{
		Log::debug("ConfigCtl.get");

		$config = [
			"version" => config("app.name") . " " . config("version.numéro") . "(" . config("version.commit_sha") . ")",
			"config" => [
				"AUTH" => [
					"LDAP" => getenv("AUTH_LDAP") == "true",
					"LOCAL" => getenv("AUTH_LOCAL") == "true",
				],
			],
		];

		if (getenv("AUTH_LDAP") === "true") {
			$config_ldap = [
				"DOMAINE" => getenv("LDAP_DOMAINE"),
				"URL_MDP_REINIT" => getenv("LDAP_URL_MDP_REINIT"),
			];

			$config["config"]["LDAP"] = $config_ldap;
		}

		$user = $request->user("api");

		$réponse = $this->valider_et_préparer_réponse(["config" => $config, "user" => $user]);

		Log::debug("ConfigCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}

	/**
	 * @return array<string>
	 */
	public function get_liens(string|null $username): array
	{
		$urlBase = Contrôleur::$urlBase;

		$ldap = getenv("AUTH_LDAP") == "true";
		$local = getenv("AUTH_LOCAL") == "true";

		$liens = [
			"self" => "$urlBase/",
		];

		if ($local || !$ldap) {
			$liens["inscrire"] = "$urlBase/user/";
		}

		if ($username) {
			$liens["user"] = "$urlBase/user/$username";
		}

		return $liens;
	}

	/**
	 * @param array<mixed> $config
	 */
	private function valider_et_préparer_réponse(array $config): JsonResponse
	{
		Log::debug("ConfigCtl.valider_et_préparer_réponse. Params : ", [$config]);

		if ($config) {
			$dto = new GénériqueDTO(
				id: "serveur",
				objet: $config,
				liens: ConfigCtl::get_liens($config["user"]?->username),
			);
			$réponse = $this->item($dto, new ConfigTransformer());
		} else {
			$réponse = null;
		}

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("ConfigCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}
}
