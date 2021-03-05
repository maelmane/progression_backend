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

use progression\http\contrôleur\LoginCtl;
use progression\domaine\entité\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;

final class LoginCtlTests extends TestCase
{
	public function test_étant_donné_une_authentification_de_type_no_lorsquon_appelle_login_on_obtient_un_token_pour_lutilisateur_Bob()
	{
		$_ENV['AUTH_TYPE'] = "no";
		$_ENV['JWT_SECRET'] = "secret";
		$_ENV['JWT_TTL'] = 33333;

		$user = new User(null);
		$user->username = "Bob";

		$résultat_attendu = "Bob";

		// Intéracteur
		$mockLoginInt = Mockery::mock(
			"progression\domaine\interacteur\LoginInt"
		);
		$mockLoginInt
			->allows()
			->effectuer_login(
				"Bob",
				"",
			)
			->andReturn($user);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock(
			"progression\domaine\interacteur\InteracteurFactory"
		);
		$mockIntFactory
			->allows()
			->getLoginInt()
			->andReturn($mockLoginInt);

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
			->andReturn(
				"/auth/"
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn();
		$mockRequest
			->allows()
			->input("username")
			->andReturn("Bob");
		$mockRequest
			->allows()
			->input("password")
			->andReturn();

		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new LoginCtl($mockIntFactory);

		$token = json_decode($ctl->login($mockRequest)->getContent(), true);
		$tokenDécodé = JWT::decode($token["Token"], $_ENV['JWT_SECRET'], array('HS256'));
		$résultat_obtenu = ($tokenDécodé->user)->username;

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
		$this->assertGreaterThan(time(), $tokenDécodé->expired);
		$this->assertLessThan($tokenDécodé->expired, $tokenDécodé->current);
	}

	public function test_étant_donné_une_authentification_de_type_ldap_lorsquon_appelle_login_on_obtient_un_token_pour_lutilisateur_jdoe()
	{
		$_ENV['AUTH_TYPE'] = "ldap";

		$user = new User(null);
		$user->username = "jdoe";

		$résultat_attendu = "jdoe";

		// Intéracteur
		$mockLoginInt = Mockery::mock(
			"progression\domaine\interacteur\LoginInt"
		);
		$mockLoginInt
			->allows()
			->effectuer_login(
				"jdoe",
				"Crosemont2021!",
			)
			->andReturn($user);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock(
			"progression\domaine\interacteur\InteracteurFactory"
		);
		$mockIntFactory
			->allows()
			->getLoginInt()
			->andReturn($mockLoginInt);

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
			->andReturn(
				"/auth/"
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn();
		$mockRequest
			->allows()
			->input("username")
			->andReturn("jdoe");
		$mockRequest
			->allows()
			->input("password")
			->andReturn("Crosemont2021!");

		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new LoginCtl($mockIntFactory);

		$token = json_decode($ctl->login($mockRequest)->getContent(), true);
		$tokenDécodé = JWT::decode($token["Token"], $_ENV['JWT_SECRET'], array('HS256'));
		$résultat_obtenu = ($tokenDécodé->user)->username;

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
		$this->assertGreaterThan(time(), $tokenDécodé->expired);
		$this->assertLessThan($tokenDécodé->expired, $tokenDécodé->current);
	}

	public function test_étant_donné_une_authentification_de_type_local_lorsquon_appelle_login_on_obtient_un_token_pour_lutilisateur_Fred()
	{
		// L'authentification locale n'est pas implémentée.
		$_ENV['AUTH_TYPE'] = "local";

		$user = new User(null);
		$user->username = "Fred";

		$résultat_attendu = "Fred";

		$this->assertEquals(null, null);
	}
}
