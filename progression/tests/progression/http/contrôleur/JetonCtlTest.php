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

use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\{User, Clé};
use progression\dao\DAOFactory;
use Illuminate\Http\Request;
use Illuminate\Auth\GenericUser;
use Firebase\JWT\JWT;

final class JetonCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		$this->user = new GenericUser(["username" => "MrGeneric", "rôle" => User::ROLE_NORMAL]);

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("MrGeneric")
			->andReturn(new User("MrGeneric"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("UtilisateurInexistant")
			->andReturn(null);

		// CléDAO
		$mockCléDAO = Mockery::mock("progression\\dao\\CléDAO");
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("MrGeneric", "clé valide")
			->andReturn(new Clé(null, (new \DateTime())->getTimestamp(), 0, Clé::PORTEE_AUTH));
		$mockCléDAO
			->shouldReceive("vérifier")
			->with("MrGeneric", "clé valide", "secret")
			->andReturn(true);
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("MrGeneric", "clé invalide")
			->andReturn(null);
		$mockCléDAO->shouldReceive("vérifier")->andReturn(false);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		$mockDAOFactory->shouldReceive("get_clé_dao")->andReturn($mockCléDAO);
		DAOFactory::setInstance($mockDAOFactory);

		//Mock du générateur de token
		GénérateurDeToken::set_instance(
			new class extends GénérateurDeToken {
				public function __construct()
				{
				}

				function générer_token($user)
				{
					return "token valide";
				}
			},
		);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

    #  AUTH LDAP
	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_LDAP_lorsquon_appelle_login_lutilisateur_sans_domaine_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=true");

		$résultat_observé = $this->call("POST", "/auth", ["username" => "UtilisateurInexistant", "password" => "password"]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->getContent());
	}



}