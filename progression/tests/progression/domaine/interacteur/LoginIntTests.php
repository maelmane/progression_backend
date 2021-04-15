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
use progression\domaine\entité\User;
use PHPUnit\Framework\TestCase;
use Mockery;

final class LoginIntTests extends TestCase
{
    public function setUp(): void{

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
            ->shouldReceive("save")
            ->andReturn(new User("Banane"));

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);
        DAOFactory::setInstance($mockDAOFactory);
    }
    
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_lutilisateur_bob_et_une_authentification_de_type_no_lorsquon_login_sans_authentification_on_obtient_un_objet_user_nommé_bob()
	{
		$_ENV["AUTH_TYPE"] = "no";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login("bob", "");

		$this->assertEquals(new User("bob"), $résultat_obtenu);
	}

	public function test_étant_donné_lutilisateur_Banane_inexistant_et_une_authentification_de_type_no_lorsquon_login_sans_authentification_il_est_créé_et_on_obtient_un_objet_user_nommé_Banane()
	{
		$_ENV["AUTH_TYPE"] = "no";

		$interacteur = new LoginInt();
		$résultat_obtenu = $interacteur->effectuer_login("Banane", "");

		$this->assertEquals(new User("Banane"), $résultat_obtenu);
	}
}
