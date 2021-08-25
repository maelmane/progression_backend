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

use progression\http\contrôleur\{LoginCtl, GénérateurDeToken};
use progression\domaine\entité\User;
use progression\dao\DAOFactory;
use Illuminate\Http\Request;
use Illuminate\Auth\GenericUser;
use Firebase\JWT\JWT;

final class LoginCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = new GenericUser(["username" => "bob", "rôle" => User::ROLE_NORMAL]);

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\dao\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("Marcel")
			->andReturn(null);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);

		//Mock du générateur de token
		GénérateurDeToken::set_instance(new class extends GénérateurDeToken{
			public function __construct(){
			}
			
			function générer_token($user)
			{
				return "token valide";
			}
		});
		
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_lutilisateur_Bob_et_une_authentification_de_type_no_lorsquon_appelle_login_on_obtient_un_token_pour_lutilisateur_Bob()
	{
		$résultat_observé = $this->call("POST", "/auth", ["username" => "bob", "password" => "test"]);

		$token = $résultat_observé->getContent();

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $token);
	}

	public function test_étant_donné_un_utilisateur_inexistant_et_une_authentification_de_type_no_lorsquon_appelle_login_lutilisateur_est_créé()
	{
		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("save")
			->once()
			->withArgs(function ($user) {
				return $user->username == "Marcel" && $user->rôle == User::ROLE_NORMAL;
			})
			->andReturn(new User("Marcel"));

		$résultat_observé = $this->call("POST", "/auth", ["username" => "Marcel", "password" => "test"]);

		$token = $résultat_observé->getContent();

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $token);
	}

	public function test_étant_donné_un_nom_dutilisateur_vide_lorsquon_appelle_login_on_obtient_une_erreur_422()
	{
		$résultat_observé = $this->call("POST", "/auth", ["username" => "", "password" => "test"]);

		$this->assertEquals(422, $résultat_observé->status());
	}

	//Intestable tant que la connexion à LDAP se fera à même l'interacteur
	/*
	   public function test_étant_donné_lutilisateur_inexistant_roger_et_une_authentification_de_type_no_lorsquon_appelle_login_on_obtient_un_code_403()
	   {
	   $_ENV['AUTH_TYPE'] = "ldap";
	   $_ENV['JWT_SECRET'] = "secret";
	   $_ENV['JWT_TTL'] = 3333;

	   $résultat_observé = $this->actingAs($this->user)->call(
	   "POST",
	   "/auth",
	   ["username"=>"marcel", "password"=>"test"]
	   );
	   
	   $this->assertEquals(403, $résultat_observé->status());
	   $this->assertEquals('{"erreur":"Accès refusé."}', $résultat_observé->getContent());
	   }
	 */
}
