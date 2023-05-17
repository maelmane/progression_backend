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

use Firebase\JWT\JWT;

class TokenTransformer extends BaseTransformer
{
	public string $type = "token";

	/**
	 * @return array<mixed>
	 */
	public function transform(string $token): array
	{
		$tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);

		return [
			/* @phpstan-ignore-next-line */
			"id" => "{$this->id}/{$tokenDécodé->current}",
			/* @phpstan-ignore-next-line */
			"username" => $tokenDécodé->username,
			/* @phpstan-ignore-next-line */
			"création" => $tokenDécodé->current,
			/* @phpstan-ignore-next-line */
			"expiration" => $tokenDécodé->expired,
			"jwt" => $token,
			"version" => 3,
			"links" => [
				/* @phpstan-ignore-next-line */
				"self" => "{$_ENV["APP_URL"]}token/{$this->id}/{$tokenDécodé->current}",
				"user" => "{$_ENV["APP_URL"]}user/{$this->id}",
			],
		];
	}
}
