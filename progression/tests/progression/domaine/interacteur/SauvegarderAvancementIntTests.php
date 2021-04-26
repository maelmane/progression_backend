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

use progression\domaine\entité\{TentativeProg, Avancement, Question};
use progression\domaine\interacteur\SauvegarderAvancementInt;
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

final class SauvegarderAvancementIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$mockUserDao = Mockery::mock("progression\dao\UserDAO");
		$mockAvancementDao = Mockery::mock("progression\dao\AvancementDAO");
        $mockTentativeDao = Mockery::mock("progression\dao\TentativeDAO");
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");

		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);
		$mockDAOFactory
			->allows()
			->get_avancement_dao()
			->andReturn($mockAvancementDao);
        $mockDAOFactory
			->allows()
			->get_tentative_prog_dao()
			->andReturn($mockTentativeDao);

		DAOFactory::setInstance($mockDAOFactory);
	}
	public function tearDown(): void
	{
		Mockery::close();
	}

    public function test_étant_donné_un_avancement_sans_tentatives_lorsquon_sauvegarde_seul_lavancement_est_enregistré_et_on_obtient_lavancement_sans_tentatives()
	{
		$résultat_attendu = new Avancement([], Question::ETAT_NONREUSSI, Question::TYPE_PROG);

		DAOFactory::getInstance()
			->get_avancement_dao()
			->shouldReceive("save")
			->once()
			->withArgs(["jdoe", "https://example.com/question", Mockery::any()])
			->andReturnArg(2);

		$interacteur = new SauvegarderAvancementInt();
		$résultat_observé = $interacteur->sauvegarder("Bob", "https://example.com/question", new Avancement([], Question::ETAT_NONREUSSI, Question::TYPE_PROG));

		$this->assertEquals($résultat_attendu, $résultat_observé);
        $this->assertEquals([], $résultat_attendu->tentatives);
        $this->assertEquals([], $résultat_observé->tentatives);
	}
    public function test_étant_donné_un_avancement_avec_tentatives_lorsquon_sauvegarde_ses_tentatives_aussi_sont_enregistrées_et_on_obtient_lavancement_avec_tentatives()
	{
		$tentative = new TentativeProg(1, "print('code')", 1616534292, false, 0, "feedback", []);
        $avancement = new Avancement([], Question::ETAT_NONREUSSI, Question::TYPE_PROG);

		DAOFactory::getInstance()
			->get_avancement_dao()
			->shouldReceive("save")
			->once()
			->withArgs(function ($user, $uri, $av) use ($avancement, $tentative) {
				return $user == "Bob" &&
					$uri == "https://example.com/question" &&
					$av == new Avancement([$tentative], Question::ETAT_NONREUSSI, Question::TYPE_PROG);
			})
			->andReturn($avancement);

		DAOFactory::getInstance()
			->get_tentative_prog_dao()
			->shouldReceive("save")
			->once()
			->withArgs(function ($user, $uri, $t) use ($tentative) {
				return $user == "Bob" && $uri == "https://example.com/question" && $t == $tentative;
			})
			->andReturn($tentative);

		$résultat_attendu = $avancement;

		$interacteur = new SauvegarderAvancementInt();
		$résultat_observé = $interacteur->sauvegarderAvancement("Bob", "https://example.com/question", $avancement);

		$this->assertEquals($résultat_attendu, $résultat_observé);
        $this->assertEquals($résultat_attendu->tentatives, $résultat_observé->tentatives);
	}
}
