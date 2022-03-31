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
		//UserDAO
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

		//Mock du générateur de token
		GénérateurDeToken::set_instance(
			new class extends GénérateurDeToken {
				public function __construct()
				{
				}

				function générer_token($user, $ressources = null, $expiration = 0)
				{
					return "token valide";
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
		$tokenAttendu = '{"Token":"token valide"}';

		$résultatObtenu = $this->actingAs($this->user)->call("POST", "/token/TurboPascal", [
			"ressources" => "ressources",
		]);
		$tokenObtenu = $résultatObtenu->getContent();

		$this->assertEquals(200, $résultatObtenu->status());
		$this->assertEquals($tokenAttendu, $tokenObtenu);
	}
}
