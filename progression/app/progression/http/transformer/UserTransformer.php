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

use progression\domaine\entité\user\{User, État, Rôle};
use progression\util\Encodage;
use League\Fractal\Resource\Collection;

class UserTransformer extends BaseTransformer
{
	public $type = "user";

	protected array $availableIncludes = ["avancements", "cles", "tokens"];

	public function transform(User $user)
	{
		$data = [
			"id" => $user->id,
			"courriel" => $user->courriel,
			"username" => $user->username,
			"état" => $user->état->value,
			"rôle" => $user->rôle->value,
			"préférences" => $user->préférences,
			"links" => (isset($user->links) ? $user->links : []) + [
				"self" => "{$_ENV["APP_URL"]}user/{$user->id}",
			],
		];

		return $data;
	}

	public function includeAvancements(User $user): Collection
	{
		$id_parent = $user->username;

		foreach ($user->avancements as $uri => $avancement) {
			$avancement->id = Encodage::base64_encode_url($uri);
			$avancement->links = [
				"user" => $_ENV["APP_URL"] . "user/{$id_parent}",
			];
		}

		return $this->collection($user->avancements, new AvancementTransformer($id_parent), "avancement");
	}

	public function includeCles(User $user): Collection
	{
		$id_parent = $user->username;

		foreach ($user->clés as $nom => $clé) {
			$clé->links = [
				"user" => $_ENV["APP_URL"] . "user/{$id_parent}",
			];
		}

		return $this->collection($user->clés, new CléTransformer($id_parent), "cle");
	}

	public function includeTokens(User $user): Collection
	{
		$id_parent = $user->username;

		//On n'enverra jamais les tokens
		return $this->collection([], new TokenTransformer($id_parent), "token");
	}
}
