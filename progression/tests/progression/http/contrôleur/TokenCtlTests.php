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
use progression\http\contrôleur\TokenCtl;

final class TokenCtlTests extends TestCase
{
	public $user;
	public $ressources;
	public $expiration;

	
	public function setUp(): void
	{
		parent::setUp();
		$this->user = new GenericUser(["username" => "TurboPascal", "rôle" => User::ROLE_NORMAL]);
		
		// UserDAO
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

		$this->expiration = time() + $_ENV["JWT_TTL"];

	}
	
	public function tearDown(): void
	{
		Mockery::close();
		GénérateurDeToken::set_instance(null);
	}

	public function test_étant_donné_un_jeton_qui_donne_accès_à_un_avancement_on_reçoit_un_token_avec_les_ressources_donnant_accès_à_cet_avancement() {
		putenv("AUTH_LDAP=true");
		putenv("AUTH_LOCAL=true");
		
		$résultatObtenu = $this->actingAs($this->user)->call("POST", "/token/TurboPascal", ["ressources" => $this->ressources, "expiration" => $this->expiration]);
		
		print_r($résultatObtenu->content());
        $token = $résultatObtenu->content();

		$tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);

		print_r($résultatObtenu->status());
		$this->assertEquals(200, $résultatObtenu->status());
		
		//$this->assertEquals($this->user->username, $tokenDécodé->username);
		//$this->assertEquals($this->user->username, $tokenDécodé->ressources);
		//$this->assertEquals($this->user->username, $tokenDécodé->expiration);
	}
}
