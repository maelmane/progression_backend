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

final class ObtenirUserIntTests extends TestCase
{
	public function test_étant_donné_un_utilisateur_Bob_lorsquon_le_cherche_par_username_on_obtient_un_objet_user_nommé_Bob()
	{
		$résultat_attendu = new User("Bob");

		$mockUserDao = Mockery::mock('progression\dao\UserDAO');
		$mockUserDao
			->allows()
			->get_user('Bob')
			->andReturn($résultat_attendu);

		$mockDAOFactory = Mockery::mock('progression\dao\DAOFactory');
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);

		$interacteur = new ObtenirUserInt($mockDAOFactory);
		$résultat_obtenu = $interacteur->get_user_par_nomusager('Bob');

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_un_utilisateur_Banane_inexistant_lorsquon_le_cherche_par_username_on_obtient_null()
	{
		$résultat_attendu = null;

		$mockUserDao = Mockery::mock('progression\dao\UserDAO');
		$mockUserDao
			->allows()
			->get_user('Banane')
			->andReturn(null);

		$mockDAOFactory = Mockery::mock('progression\dao\DAOFactory');
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);

		$interacteur = new ObtenirUserInt($mockDAOFactory);
		$résultat_obtenu = $interacteur->get_user_par_nomusager('Banane');

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

}
