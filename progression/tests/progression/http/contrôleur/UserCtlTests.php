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

use progression\dao\DAOFactory;
use progression\domaine\entité\{Avancement, User};
use Illuminate\Auth\GenericUser;

final class UserCtlTests extends TestCase
{
	public $user;
	public function setUp(): void
	{
		parent::setUp();

		$this->user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);

		$_ENV["APP_URL"] = "https://example.com/";

		$user = new User("jdoe");
		$user->avancements = [
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" => new Avancement(),
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction" => new Avancement(),
		];

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("jdoe")
			->andReturn($user);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_le_nom_dun_utilisateur_lorsquon_appelle_get_on_obtient_lutilisateur_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call("GET", "/user/jdoe");

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/userCtlTest_1.json",
			$résultat_obtenu->getContent(),
		);
	}
}
