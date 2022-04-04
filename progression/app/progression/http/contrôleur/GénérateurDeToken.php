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

class GénérateurDeToken
{
	private static ?GénérateurDeToken $instance = null;

	private function __construct()
	{
	}

	static function get_instance()
	{
		if (GénérateurDeToken::$instance == null) {
			GénérateurDeToken::$instance = new GénérateurDeToken();
		}

		return GénérateurDeToken::$instance;
	}

	static function set_instance(?GénérateurDeToken $générateur)
	{
		GénérateurDeToken::$instance = $générateur;
	}

	function générer_token($user, $ressources = null, $expiration = 0)
	{
		$payload = [
			"username" => $user->username,
			"current" => time(),
			"expired" => $expiration,
			"ressources" => $ressources,
		];

		$token = JWT::encode($payload, $_ENV["JWT_SECRET"], "HS256");

		return $token;
	}
}
