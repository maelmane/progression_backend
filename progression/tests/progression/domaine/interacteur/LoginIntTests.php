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

use progression\domaine\entité\User;
use PHPUnit\Framework\TestCase;
use \Mockery;

final class LoginIntTests extends TestCase
{
	public function test_étant_donné_lutilisateur_Bob_et_une_authentification_de_type_no_lorsquon_login_sans_authentification_on_obtient_un_objet_user_nommé_Bob()
	{
		$_ENV["AUTH_TYPE"] = "no";

		$résultat_attendu = new User("Bob");

		$mockUserDao = Mockery::mock("progression\dao\UserDAO");
		$mockUserDao
			->allows()
			->get_user("Bob")
			->andReturn($résultat_attendu);

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);

		$interacteur = new LoginInt($mockDAOFactory);
		$résultat_obtenu = $interacteur->effectuer_login("Bob", "");

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_Banane_inexistant_et_une_authentification_de_type_no_lorsquon_login_sans_authentification_il_est_créé_et_on_obtient_un_objet_user_nommé_Banane()
	{
		$_ENV["AUTH_TYPE"] = "no";

		$résultat_attendu = new User("Banane");

		$mockUserDao = Mockery::mock("progression\dao\UserDAO");
		$mockUserDao
			->allows()
			->get_user("Banane")
			->andReturn(null);

		$mockUserDao->allows("save")->andReturn($résultat_attendu);

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);

		$interacteur = new LoginInt($mockDAOFactory);
		$résultat_obtenu = $interacteur->effectuer_login("Banane", "");

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_jdoe_et_une_authentification_de_type_ldap_lorsquon_login_ldap_on_obtient_un_objet_user_nommé_jdoe()
	{
		// À faire
		$_ENV["AUTH_TYPE"] = "ldap";

		$résultat_attendu = new User("jdoe");

		$this->assertEquals(null, null);
	}

	public function test_étant_donné_lutilisateur_Marcel_inexistant_et_une_authentification_de_type_ldap_lorsquon_login_ldap_on_obtient_null()
	{
		// À faire
		$_ENV["AUTH_TYPE"] = "ldap";

		$résultat_attendu = null;

		$this->assertEquals(null, null);
	}

	public function test_étant_donné_lutilisateur_Fred_et_une_authentification_de_type_local_lorsquon_login_local_on_obtient_un_objet_user_nommé_Fred()
	{
		// À faire
		$_ENV["AUTH_TYPE"] = "local";

		$résultat_attendu = new User("Fred");

		$this->assertEquals(null, null);
	}

	public function test_étant_donné_lutilisateur_Lea_inexistant_et_une_authentification_de_type_local_lorsquon_login_local_on_obtient_null()
	{
		// À faire
		$_ENV["AUTH_TYPE"] = "local";

		$résultat_attendu = null;

		$this->assertEquals(null, null);
	}
}
