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

require_once __DIR__ . '/../../../TestCase.php';

use progression\http\contrôleur\UserCtl;
use progression\domaine\entité\User;
use Illuminate\Http\Request;

final class UserCtlTests extends TestCase
{
	public function test_étant_donné_le_nom_dun_utilisateur_lorsquon_appelle_get_on_obtient_lutilisateur_et_ses_relations_sous_forme_json()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$user = new User(null);
		$user->username = "Bob";

		$résultat_attendu = [
			"data" => [
				"type" => "user",
				"id" => "Bob",
				"attributes" => [
					"username" => "Bob",
					"rôle" => "0"
				],
				"links" => [
					"self" => "https://example.com/user/Bob"
				],
			],
		];

		// Intéracteur
		$mockObtenirUserInt = Mockery::mock(
			"progression\domaine\interacteur\ObtenirUserInt"
		);
		$mockObtenirUserInt
			->allows()
			->get_user_par_nomusager(
				"Bob"
			)
			->andReturn($user);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock(
			"progression\domaine\interacteur\InteracteurFactory"
		);
		$mockIntFactory
			->allows()
			->getObtenirUserInt()
			->andReturn($mockObtenirUserInt);

		// Requête
		$mockRequest = Mockery::mock("Illuminate\Http\Request");
		$mockRequest
			->allows()
			->ip()
			->andReturn("127.0.0.1");
		$mockRequest
			->allows()
			->method()
			->andReturn("GET");
		$mockRequest
			->allows()
			->path()
			->andReturn("/user");
		$mockRequest
			->allows()
			->all()
			->andReturn();
		$mockRequest
			->allows()
			->route("username")
			->andReturn("Bob");
		$mockRequest
			->allows()
			->query("include")
			->andReturn();

		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new UserCtl($mockIntFactory);

		$this->assertEquals(
			$résultat_attendu,
			json_decode(
				$ctl
					->get(
						$mockRequest
					)
					->getContent(),
				true
			)
		);
	}
}
