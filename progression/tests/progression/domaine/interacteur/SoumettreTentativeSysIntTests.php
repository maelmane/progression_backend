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

use progression\domaine\entité\{
	Exécutable,
	Avancement,
	Question,
	QuestionSys,
	RésultatSys,
	TentativeSys,
	TestSys,
	User,
};
use progression\dao\DAOFactory;
use progression\dao\tentative\TentativeSysDAO;
use PHPUnit\Framework\TestCase;
use Mockery;
use progression\dao\question\QuestionDAO;

final class SoumettreTentativeSysIntTests extends TestCase
{
	protected static $questionTests;
	protected static $questionReponseCourte;
	protected static $tentativeSoumiseIncorrecte;

	public function setUp(): void
	{
		parent::setUp();

		//Mock User
		$mockUserDao = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDao
			->allows()
			->get_user("jdoe")
			->andReturn(new User("jdoe"));

		// Mock DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDao);
		DAOFactory::setInstance($mockDAOFactory);
	}
}
