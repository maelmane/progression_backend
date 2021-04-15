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

require_once __DIR__ . "/../../../TestCase.php";

use progression\dao\DAOFactory;
use progression\domaine\entité\{Avancement, User};
use progression\http\contrôleur\UserCtl;
use Illuminate\Http\Request;

final class UserCtlTests extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        $_ENV["APP_URL"] = "https://example.com/";

        $user = new User("jdoe");
		$user->avancements = [
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" => new Avancement(),
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction" => new Avancement(),
		];

        // UserDAO
		$mockUserDAO = Mockery::mock("progression\dao\UserDAO");
		$mockUserDAO
            ->shouldReceive("get_user")
            ->with("jdoe")
            ->andReturn($user);
		
		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->shouldReceive("get_user_dao")
			->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
    }
    
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_le_nom_dun_utilisateur_lorsquon_appelle_get_on_obtient_lutilisateur_et_ses_relations_sous_forme_json()
	{
		$résultat_attendu = [
			"data" => [
				"type" => "user",
				"id" => "jdoe",
				"attributes" => [
					"username" => "jdoe",
					"rôle" => "0",
				],
				"links" => [
					"self" => "https://example.com/user/jdoe",
				],
				"relationships" => [
					"avancements" => [
						"data" => [
							[
								"type" => "avancement",
								"id" =>
									"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
							],
							[
								"type" => "avancement",
								"id" =>
									"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfYXV0cmVfZm9uY3Rpb24",
							],
						],
						"links" => [
							"self" => "https://example.com/user/jdoe/relationships/avancements",
							"related" => "https://example.com/user/jdoe/avancements",
						],
					],
				],
			],
			"included" => [
				[
					"type" => "avancement",
					"id" =>
						"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
					"attributes" => [
						"état" => 0,
					],
					"links" => [
						"self" =>
							"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
						"tentative" => "https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
						"related" => "https://example.com/user/jdoe",
					],
					"relationships" => [
						"tentatives" => [
							"links" => [
								"self" =>
									"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/relationships/tentatives",
								"related" =>
									"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives",
							],
						],
					],
				],
				[
					"type" => "avancement",
					"id" =>
						"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfYXV0cmVfZm9uY3Rpb24",
					"attributes" => [
						"état" => 0,
					],
					"links" => [
						"self" =>
							"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfYXV0cmVfZm9uY3Rpb24",
						"tentative" => "https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfYXV0cmVfZm9uY3Rpb24",
						"related" => "https://example.com/user/jdoe",
					],
					"relationships" => [
						"tentatives" => [
							"links" => [
								"self" =>
									"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfYXV0cmVfZm9uY3Rpb24/relationships/tentatives",
								"related" =>
									"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfYXV0cmVfZm9uY3Rpb24/tentatives",
							],
						],
					],
				],
			],
		];

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
			->query("include")
			->andReturn("avancements");

		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new UserCtl();
		$résultat_obtenu = $ctl->get($mockRequest, "jdoe");

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}
}
