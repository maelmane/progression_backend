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
use progression\domaine\entité\User;
use Illuminate\Auth\GenericUser;

final class AuthServiceProviderCtlTests extends TestCase
{
	public function setUp(): void
	{
		//UserDAOz
		parent::setUp();
		$this->user = new GenericUser(["username" => "UtilisateurLambda", "rôle" => User::ROLE_NORMAL]);

		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("UtilisateurLambda")
			->andReturn(new User("UtilisateurLambda"));

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_expire_dans_30_minutes_lautorisation_daccès_est_donnée_par_le_système_avec_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = time() + 30 * 60;
		$token = GénérateurDeToken::get_instance()->générer_token("UtilisateurLambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/UtilisateurLambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_dont_la_date_dexpiration_est_0_lautorisation_daccès_est_donnée_avec_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("UtilisateurLambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/UtilisateurLambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_dont_la_date_dexpiration_est_échue_depuis_1_seconde_lautorisation_daccès_est_refusée_avec_un_code_401()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = time() - 1;
		$token = GénérateurDeToken::get_instance()->générer_token("UtilisateurLambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/UtilisateurLambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(401, $résultatObtenu->status());
	}

	//TODO: mock pour avancement, ou mettre les mock dans une classe parent

	// public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_essaie_deffectuer_un_get_sur_un_avancement_et_que_le_token_lui_permet_cela_lautorisation_est_donnée_avec_le_code_200()
	// {
	// 	$ressources = '{
	// 		"ressources": {
	// 		  "url": "avancement/UtilisateurLambda/*",
	// 		  "method": "GET"
	// 		}
	// 	  }';
	// 	$expiration = 0;
	// 	$token = GénérateurDeToken::get_instance()->générer_token("UtilisateurLambda", $ressources, $expiration);
	// 	$method = "GET";
	// 	$route = "/avancement/UtilisateurLambda/questionuri";
	// 	$headers = ["HTTP_Authorization" => "Bearer " . $token];

	// 	$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

	// 	$this->assertEquals(200, $résultatObtenu->status());
	// }

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_essaie_deffectuer_un_post_alors_quil_ne_peut_faire_quun_get_lautorisation_daccès_est_refusée_avec_un_code_405()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("UtilisateurLambda", $ressources, $expiration);
		$method = "POST";
		$route = "/user/UtilisateurLambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(405, $résultatObtenu->status());
	}
}
