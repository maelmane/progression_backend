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
use progression\dao\DAOFactory;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;

final class LoginCtlTests extends TestCase
{

    public function setUp() : void
    {
        parent::setUp();
        
        // UserDAO
		$mockUserDAO = Mockery::mock("progression\dao\UserDAO");
		$mockUserDAO
		->shouldReceive("get_user")
		->with("Bob")
		->andReturn(new User("Bob"));
		$mockUserDAO
		->shouldReceive("get_user")
		->with("jdoe")
		->andReturn(new User("jdoe"));
		$mockUserDAO
		->shouldReceive("get_user")
		->with("Marcel")
		->andReturn(null);
		
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

    public function test_étant_donné_lutilisateur_Bob_et_une_authentification_de_type_no_lorsquon_appelle_login_on_obtient_un_token_pour_lutilisateur_Bob()
	{
		$_ENV['AUTH_TYPE'] = "no";
		$_ENV['JWT_SECRET'] = "secret";
		$_ENV['JWT_TTL'] = 3333;

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
			->andReturn("/auth/");
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
		$ctl = new LoginCtl();
		$résultat_observé = $ctl->login($mockRequest);

		$token = json_decode($résultat_observé->getContent(), true);
		$tokenDécodé = JWT::decode($token["Token"], $_ENV['JWT_SECRET'], ['HS256']);
		$username_obtenu = $tokenDécodé->user->username;

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals("Bob", $username_obtenu);
		$this->assertGreaterThan(time(), $tokenDécodé->expired);
		$this->assertEquals(3333, $tokenDécodé->expired - $tokenDécodé->current);
	}

}
