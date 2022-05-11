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
use progression\domaine\entité\{Avancement, User};
use Illuminate\Auth\GenericUser;

final class AvancementsCtlTests extends ContrôleurTestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$_ENV["APP_URL"] = "https://example.com/";

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob"));

		// Avancement
		$avancements = ["uri_a" => new Avancement(), "uri_b" => new Avancement()];

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_tous")
			->with("jdoe")
			->andReturn($avancements);
		$mockAvancementDAO
			->shouldReceive("get_tous")
			->with("bob")
			->andReturn([]);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		$mockDAOFactory->shouldReceive("get_avancement_dao")->andReturn($mockAvancementDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_utilisateur_ayant_des_avancements_lorsquon_appelle_get_on_obtient_tous_les_avancements_et_ses_relations_sous_forme_json()
	{
		$user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);
		$résultat_observé = $this->actingAs($user)->call("GET", "/user/jdoe/avancements");

		$this->assertResponseStatus(200);
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementCtlTestsArray.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_sans_avancement_lorsquon_appelle_get_on_obtient_un_tableau_vide()
	{
		$user = new GenericUser(["username" => "bob", "rôle" => User::ROLE_NORMAL]);
		$résultat_observé = $this->actingAs($user)->call("GET", "/user/bob/avancements");

		$this->assertResponseStatus(200);
		$this->assertJsonStringEqualsJsonString('{"data":[]}', $résultat_observé->getContent());
	}
}
