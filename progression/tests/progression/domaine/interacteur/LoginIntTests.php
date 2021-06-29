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

namespace progression\domaine\interacteur;

use progression\dao\DAOFactory;
use progression\domaine\entité\{User, Clé};
use PHPUnit\Framework\TestCase;
use Mockery;

final class LoginIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$mockUserDao = Mockery::mock("progression\dao\UserDAO");
		$mockUserDao
			->allows()
			->get_user("bob")
			->andReturn(new User("bob"));
		$mockUserDao
			->allows()
			->get_user("Banane")
			->andReturn(null);
		$mockUserDao
			->allows()
			->vérifier_password(Mockery::Any(), "password")
			->andReturn(true);
		$mockUserDao
			->allows()
			->vérifier_password(Mockery::Any(), Mockery::Any())
			->andReturn(false);

		$mockUserDao->shouldReceive("save")->andReturn(new User("Banane"));
		$mockUserDao->shouldReceive("set_password")->withArgs(function ($user, $password) {
			return $user->username == "Banane" && $password == "password";
		});

		$mockCléDao = Mockery::mock("progression\dao\CléDAO");
		$mockCléDao
			->allows()
			->get_clé("bob", "clé_valide")
			->andReturn(new Clé("clé_valide", (new \DateTime())->getTimestamp(), 0, Clé::PORTEE_AUTH));
		$mockCléDao
			->allows()
			->get_clé("bob", "clé_expirée")
			->andReturn(
				new Clé(
					"clé_valide",
					(new \DateTime())->getTimestamp() - 2,
					(new \DateTime())->getTimestamp() - 1,
					Clé::PORTEE_AUTH,
				),
			);
		$mockCléDao
			->allows()
			->get_clé("bob", "clé_révoquée")
			->andReturn(new Clé("clé_valide", (new \DateTime())->getTimestamp(), 0, Clé::PORTEE_REVOQUEE));

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);
		$mockDAOFactory
			->allows()
			->get_clé_dao()
			->andReturn($mockCléDao);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_lutilisateur_null_lorsquon_login_obtient_null()
	{
		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_identifiant(null);

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_vide_lorsquon_login_obtient_null()
	{
		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_identifiant("");

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_bob_et_une_authentification_de_type_no_lorsquon_login_sans_mot_de_passe_on_obtient_un_objet_user_nommé_bob()
	{
		$_ENV["AUTH_TYPE"] = "no";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_identifiant("bob");

		$this->assertEquals(new User("bob"), $résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_Banane_inexistant_et_une_authentification_de_type_no_lorsquon_login_sans_mot_de_passe_il_est_créé_et_on_obtient_un_objet_user_nommé_Banane()
	{
		$_ENV["AUTH_TYPE"] = "no";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_identifiant("Banane");

		$this->assertEquals(new User("Banane"), $résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_existant_bob_et_une_authentification_de_type_local_lorsquon_login_avec_mdp_correct_on_obtient_un_objet_user_nommé_bob()
	{
		$_ENV["AUTH_TYPE"] = "local";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_identifiant("bob", "password");

		$this->assertEquals(new User("bob"), $résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_existant_bob_et_une_authentification_de_type_local_lorsquon_login_avec_mdp_incorrect_on_obtient_null()
	{
		$_ENV["AUTH_TYPE"] = "local";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_identifiant("bob", "pas mon mot de passe");

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_Banane_inexistant_et_une_authentification_de_type_local_lorsquon_login_on_obtient_null()
	{
		$_ENV["AUTH_TYPE"] = "local";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_identifiant("Banane", "password");

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_existant_bob_et_une_clé_dauthentification_valide_lorsquon_login_on_obtient_un_user_bob()
	{
		$_ENV["AUTH_TYPE"] = "local";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_clé("bob", "clé_valide");

		$this->assertEquals(new User("bob"), $résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_existant_bob_et_une_clé_dauthentification_expirée_lorsquon_login_on_obtient_null()
	{
		$_ENV["AUTH_TYPE"] = "local";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_clé("bob", "clé_expirée");

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_existant_bob_et_une_clé_dauthentification_révoquée_lorsquon_login_on_obtient_null()
	{
		$_ENV["AUTH_TYPE"] = "local";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login_par_clé("bob", "clé_révoquée");

		$this->assertNull($résultat_obtenu);
	}
}
