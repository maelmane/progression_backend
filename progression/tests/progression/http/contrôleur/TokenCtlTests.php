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

use progression\ContrôleurTestCase;

use progression\dao\DAOFactory;
use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\User;
use Illuminate\Auth\GenericUser;

final class TokenCtlTests extends ContrôleurTestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$this->user = new GenericUser(["username" => "utilisateur_lambda", "rôle" => User::RÔLE::NORMAL]);

		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_lambda")
			->andReturn(new User("utilisateur_lambda"));

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

	public function test_étant_donné_un_token_qui_donne_accès_à_une_ressource_lorsquon_effectue_un_post_on_obtient_un_token_avec_les_ressources_voulues()
	{
		$tokenAttendu = '{"Token":"token valide"}';

		$résultatObtenu = $this->actingAs($this->user)->call("POST", "/token/utilisateur_lambda", [
			"ressources" => "ressources",
		]);
		$tokenObtenu = $résultatObtenu->getContent();

		$this->assertEquals(200, $résultatObtenu->status());
		$this->assertEquals($tokenAttendu, $tokenObtenu);
	}
}
