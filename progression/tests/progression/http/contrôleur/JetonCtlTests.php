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
use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\{User};
use Illuminate\Http\Request;
use Illuminate\Auth\GenericUser;
use Firebase\JWT\JWT;
use progression\http\contrôleur\JetonCtl;

final class JetonCtlTests extends TestCase
{
	public $user;
	
	public function setUp(): void
	{
		parent::setUp();
		$this->user = new GenericUser(["username" => "MrGeneric", "rôle" => User::ROLE_NORMAL]);
		
		$_ENV["APP_URL"] = "https://example.com/";
		
		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
		->shouldReceive("get_user")
		->with("MrGeneric")
		->andReturn(new User("MrGeneric"));
		
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}
	
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_création_de_jeton_qui_donne_accès_à_un_avancement() {
		putenv("AUTH_LDAP=true");
		putenv("AUTH_LOCAL=true");
		
		$response = $this->actingAs($this->user)->call(
			"POST", 
			"/jeton/MrGeneric", 
			["username" => "MrGeneric", 
			"idRessource" => "IdentifiantRessource", 
			"typeRessource" => "avancement", 
			"uriQuestion" => "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction"]);
		
		$this->assertEquals(200, $response->status());
		//TODO: récupérer les informations dans le body de la réponse.
	}

	// public function test_création_de_jeton_qui_donne_accès_à_un_avancement_avec_paramètre_vide() {
	// 	putenv("AUTH_LDAP=true");
	// 	putenv("AUTH_LOCAL=true");
		
	// 	$response = $this->actingAs($this->user)->call("POST", "/jeton/MrGeneric", ["username" => "MrGeneric", "idRessource" => "IdentifiantRessource", "typeRessource" => ""]);
		
	// 	$this->assertEquals(400, $response->status());
	// }

}