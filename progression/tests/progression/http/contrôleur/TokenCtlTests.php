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

use progression\TestCase;

use Firebase\JWT\JWT;
use progression\dao\DAOFactory;
use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\{User};
use Illuminate\Auth\GenericUser;

final class TokenCtlTests extends TestCase
{
	public $user;
	public $ressources;
	public $expiration;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = new GenericUser(["username" => "TurboPascal", "rôle" => User::ROLE_NORMAL]);

		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("TurboPascal")
			->andReturn(new User("TurboPascal"));

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);

		$this->ressources = '{
			"ressources": [
			  {
				"url": "avancement/username/uri_question",
              	"method": "GET"
			  }
			]
		  }';

		$this->expiration = 0;

		GénérateurDeToken::set_instance(
		new class extends GénérateurDeToken {
			public function __construct()
			{
			}

			function générer_token($user, $ressources = null, $expiration = 0)
			{
				$payload = [
					"username" => $user->username,
					"current" => strtotime("6 october 2022"),
					"expired" => strtotime("8 october 2022"),
					"ressources" => $ressources
				];

				$JWT = JWT::encode($payload, $_ENV["JWT_SECRET"], "HS256");
				return $JWT;
			}
		},
		);
	}

	public function tearDown(): void
	{
		Mockery::close();
		GénérateurDeToken::set_instance(null);
	}

	public function test_étant_donné_un_jeton_qui_donne_accès_à_un_avancement_on_reçoit_un_token_avec_les_ressources_donnant_accès_à_cet_avancement()
	{
		$tokenAttendu = '{"Token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IlR1cmJvUGFzY2FsIiwiY3VycmVudCI6MTY2NTAxNDQwMCwiZXhwaXJlZCI6MTY2NTE4NzIwMCwicmVzc291cmNlcyI6Intcblx0XHRcdFwicmVzc291cmNlc1wiOiBbXG5cdFx0XHQgIHtcblx0XHRcdFx0XCJ1cmxcIjogXCJhdmFuY2VtZW50XC91c2VybmFtZVwvdXJpX3F1ZXN0aW9uXCIsXG4gICAgICAgICAgICAgIFx0XCJtZXRob2RcIjogXCJHRVRcIlxuXHRcdFx0ICB9XG5cdFx0XHRdXG5cdFx0ICB9In0.2TuNjLVqper8NbQ5Y3zbnOTsKKS-ZUu92HvBuYtc1Ik"}';

		$résultatObtenu = $this->actingAs($this->user)->call("POST", "/token/TurboPascal", ["ressources" => $this->ressources, "expiration" => $this->expiration]);
		$tokenObtenu = $résultatObtenu->getContent();

		$this->assertEquals(200, $résultatObtenu->status());
		$this->assertEquals($tokenAttendu, $tokenObtenu);
	}
}
