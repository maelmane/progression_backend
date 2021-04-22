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

namespace progression\dao;

use PHPUnit\Framework\TestCase;
use progression\dao\DAOFactory;

final class TentativeDAOTests extends TestCase
{
	public function setUp(): void
	{
		EntitéDAO::get_connexion()->begin_transaction();
		DAOFactory::setInstance(null);
	}

	public function tearDown(): void
	{
		EntitéDAO::get_connexion()->rollback();
	}

	public function test_étant_donné_une_tentative_inexistante_lorsquon_récupère_la_tentative_on_obtient_null()
	{
		$résultat_attendu = null;

		$résultat_observé = (new TentativeDAO())->get_tentative("exemple", "exemple", 0);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
