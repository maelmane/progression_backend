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
use progression\domaine\entité\user\{User, Rôle, État};
use Illuminate\Auth\GenericUser;
use Carbon\Carbon;

final class TokenCtlTests extends ContrôleurTestCase
{
	public function setUp(): void
	{
		parent::setUp();

		Carbon::setTestNowAndTimezone(Carbon::create(2001, 5, 21, 12));

		putenv("APP_URL=https://example.com");
		putenv("APP_VERSION=1.2.3");

		$this->user = new GenericUser([
			"username" => "utilisateur_lambda",
			"rôle" => Rôle::NORMAL,
			"état" => État::ACTIF,
		]);

		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_lambda")
			->andReturn(new User(username: "utilisateur_lambda", date_inscription: 0));

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		GénérateurDeToken::set_instance(null);
	}

	public function test_étant_donné_un_token_qui_donne_accès_à_une_ressource_lorsquon_effectue_un_post_on_obtient_un_token_avec_les_ressources_voulues_sans_expiration()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call("POST", "/user/utilisateur_lambda/tokens", [
			"ressources" => "ressources",
		]);
		$résultat_observé = $résultat_obtenu;

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/token_ressources.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_token_qui_donne_accès_à_un_date_dexpiration_lorsquon_effectue_un_post_on_obtient_un_token_avec_expiration()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call("POST", "/user/utilisateur_lambda/tokens", [
			"ressources" => "ressources",
			"expiration" => 1685831340,
		]);
		$résultat_observé = $résultat_obtenu;

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/token_ressources_avec_expiration.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_token_sans_ressource_ni_expiration_lorsquon_effectue_un_post_on_obtient_un_token_universel_sans_expiration()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call("POST", "/user/utilisateur_lambda/tokens", []);
		$résultat_observé = $résultat_obtenu;

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/token_ressources_universel.json",
			$résultat_observé->getContent(),
		);
	}
}
