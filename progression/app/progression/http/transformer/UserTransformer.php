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

use progression\domaine\entité\User;

use progression\util\Encodage;

class UserTransformer extends BaseTransformer
{
	public $type = "user";

	protected array $availableIncludes = ["avancements", "cles"];

	public function transform(User $user)
	{
		$data = [
			"id" => $user->username,
			"username" => $user->username,
			"rôle" => $user->rôle,
			"links" => (isset($user->links) ? $user->links : []) + [
				"self" => "{$_ENV["APP_URL"]}user/{$user->username}",
			],
		];

		return $data;
	}

	public function includeAvancements(User $user)
	{
		foreach ($user->avancements as $uri => $avancement) {
			$avancement->id = "{$user->username}/" . Encodage::base64_encode_url($uri);
			$avancement->links = [
				"related" => $_ENV["APP_URL"] . "user/{$user->username}",
			];
		}

		return $this->collection($user->avancements, new AvancementTransformer(), "avancement");
	}

	public function includeCles(User $user)
	{
		foreach ($user->clés as $nom => $clé) {
			$clé->id = "{$user->username}/" . $nom;
			$clé->links = [
				"related" => $_ENV["APP_URL"] . "user/{$user->username}",
			];
		}

		return $this->collection($user->clés, new CléTransformer(), "cle");
	}
}
