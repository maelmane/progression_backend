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
use progression\dao\DAOFactory;
use progression\dao\tentative\TentativeDAO;
use PHPUnit\Framework\TestCase;
use Mockery;

final class SauvegarderTentativeProgIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$mockAvancementDao = Mockery::mock("progression\\dao\\AvancementDAO");

		$mockTentativeDao = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
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
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_une_première_tentative_ratée_lorsquon_la_sauvegarde_on_obtient_la_tentative()
	{
		$tentative = new TentativeProg(1, "print('code')", 1616534292, false, 0, "feedback", []);
		DAOFactory::getInstance()
			->get_tentative_prog_dao()
			->shouldReceive("save")
			->once()
			->withArgs(function ($user, $uri, $t) use ($tentative) {
				return $user == "Bob" && $uri == "https://example.com/question" && $t == $tentative;
			})
			->andReturn($tentative);

		$résultat_attendu = $tentative;

		$interacteur = new SauvegarderTentativeProgInt();
		$résultat_observé = $interacteur->sauvegarder("Bob", "https://example.com/question", $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_première_tentative_réussie_lorsquon_la_sauvegarde_on_obtient_la_tentative()
	{

		$tentative = new TentativeProg(1, "print('code')", 1616534292, true, 1, "feedback", []);

		DAOFactory::getInstance()
			->get_tentative_prog_dao()
			->shouldReceive("save")
			->once()
			->withArgs(function ($user, $uri, $t) use ($tentative) {
				return $user == "Bob" && $uri == "https://example.com/question" && $t == $tentative;
			})
			->andReturn($tentative);

		$résultat_attendu = $tentative;

		$interacteur = new SauvegarderTentativeProgInt();
		$résultat_observé = $interacteur->sauvegarder("Bob", "https://example.com/question", $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_deuxième_tentative_nonréussie_à_une_question_non_réussie_lorsquon_la_sauvegarde_on_obtient_la_tentative()
	{
		$tentative = new TentativeProg(1, "print('code')", 1616534292, false, 0, "feedback", []);

		DAOFactory::getInstance()
			->get_tentative_prog_dao()
			->shouldReceive("save")
			->once()
			->withArgs(function ($user, $uri, $t) use ($tentative) {
				return $user == "Bob" && $uri == "https://example.com/question" && $t == $tentative;
			})
			->andReturn($tentative);

		$résultat_attendu = $tentative;

		$interacteur = new SauvegarderTentativeProgInt();
		$résultat_observé = $interacteur->sauvegarder("Bob", "https://example.com/question", $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_deuxième_tentative_réussie_à_une_question_non_réussie_lorsquon_la_sauvegarde_on_obtient_la_tentative()
	{
		$tentative = new TentativeProg(1, "print('code')", 1616534292, true, 1, "feedback", []);

		DAOFactory::getInstance()
			->get_tentative_prog_dao()
			->shouldReceive("save")
			->once()
			->withArgs(function ($user, $uri, $t) use ($tentative) {
				return $user == "Bob" && $uri == "https://example.com/question" && $t == $tentative;
			})
			->andReturn($tentative);

		$résultat_attendu = $tentative;

		$interacteur = new SauvegarderTentativeProgInt();
		$résultat_observé = $interacteur->sauvegarder("Bob", "https://example.com/question", $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_deuxième_tentative_nonréussie_à_une_question_réussie_lorsquon_la_sauvegarde_lavancement_nest_pas_sauvegardé_et_on_obtient_la_tentative()
	{
		$tentative = new TentativeProg(1, "print('code')", 1616534292, false, 0, "feedback", []);

		DAOFactory::getInstance()
			->get_avancement_dao()
			->shouldReceive("get_avancement")
			->with("Bob", "https://example.com/question")
			->andReturn(
				new Avancement(Question::ETAT_REUSSI, Question::TYPE_PROG, [
					new TentativeProg(1, "print('code')", 1616531000, true, 1, "feedback", []),
				]),
			);
		DAOFactory::getInstance()
			->get_avancement_dao()
			->shouldNotReceive("save");

		DAOFactory::getInstance()
			->get_tentative_prog_dao()
			->shouldReceive("save")
			->once()
			->withArgs(function ($user, $uri, $t) use ($tentative) {
				return $user == "Bob" && $uri == "https://example.com/question" && $t == $tentative;
			})
			->andReturn($tentative);

		$résultat_attendu = $tentative;

		$interacteur = new SauvegarderTentativeProgInt();
		$résultat_observé = $interacteur->sauvegarder("Bob", "https://example.com/question", $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_deuxième_tentative_réussie_à_une_question_réussie_lorsquon_la_sauvegarde_lavancement_nest_pas_sauvegardé_et_on_obtient_la_tentative()
	{
		$tentative = new TentativeProg(1, "print('code')", 1616534292, true, 1, "feedback", []);

		DAOFactory::getInstance()
			->get_avancement_dao()
			->shouldReceive("get_avancement")
			->with("Bob", "https://example.com/question")
			->andReturn(
				new Avancement(Question::ETAT_REUSSI, Question::TYPE_PROG, [
					new TentativeProg(1, "print('code')", 1616531000, true, 1, "feedback", []),
				]),
			);
		DAOFactory::getInstance()
			->get_avancement_dao()
			->shouldNotReceive("save");

		DAOFactory::getInstance()
			->get_tentative_prog_dao()
			->shouldReceive("save")
			->once()
			->withArgs(function ($user, $uri, $t) use ($tentative) {
				return $user == "Bob" && $uri == "https://example.com/question" && $t == $tentative;
			})
			->andReturn($tentative);

		$résultat_attendu = $tentative;

		$interacteur = new SauvegarderTentativeProgInt();
		$résultat_observé = $interacteur->sauvegarder("Bob", "https://example.com/question", $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
