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

use progression\domaine\entité\{TentativeProg, Avancement, Question, QuestionProg, User};
use progression\domaine\interacteur\SauvegarderAvancementInt;
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use progression\dao\question\QuestionDAO;
use Mockery;

final class SauvegarderAvancementIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->allows()
			->get_user("jdoe")
			->andReturn(new User("jdoe"));

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://example.com/question")
			->andReturn(null);

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDAO);
		$mockDAOFactory
			->allows()
			->get_avancement_dao()
			->andReturn($mockAvancementDAO);

		$question = new QuestionProg();
		$question->uri = "file:///prog1/les_fonctions/appeler_une_fonction/info.yml";

		$mockQuestionDao = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDao
			->shouldReceive("get_question")
			->with("file:///prog1/les_fonctions/appeler_une_fonction/info.yml")
			->andReturn($question);
		$mockQuestionDao
			->shouldReceive("get_question")
			->with("file:///test/de/chemin/non/valide")
			->andReturn(null);

		$mockDAOFactory
			->allows()
			->get_question_dao()
			->andReturn($mockQuestionDao);
		DAOFactory::setInstance($mockDAOFactory);

		DAOFactory::setInstance($mockDAOFactory);
	}
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_avancement_sans_tentatives_lorsquon_sauvegarde_seul_lavancement_est_enregistré_et_on_obtient_lavancement_sans_tentatives()
	{
		DAOFactory::getInstance()
			->get_avancement_dao()
			->shouldReceive("save")
			->once()
			->withArgs(["jdoe", "file:///prog1/les_fonctions/appeler_une_fonction/info.yml", Mockery::any()])
			->andReturnArg(2);
		$interacteur = new SauvegarderAvancementInt();
		$résultat_observé = $interacteur->sauvegarder(
			"jdoe",
			"file:///prog1/les_fonctions/appeler_une_fonction/info.yml",
			new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_PROG),
		);

		$résultat_attendu = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_PROG);

		$this->assertEquals($résultat_attendu, $résultat_observé);
		$this->assertEquals([], $résultat_observé->tentatives);
	}
	public function test_étant_donné_un_avancement_avec_tentatives_lorsquon_sauvegarde_ses_tentatives_aussi_sont_enregistrées_et_on_obtient_lavancement_avec_tentatives()
	{
		$tentative = new TentativeProg(1, "print('code')", 1616534292, false, 0, "feedback", []);
		$avancement = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_PROG, [$tentative]);

		DAOFactory::getInstance()
			->get_avancement_dao()
			->shouldReceive("save")
			->once()
			->withArgs(["jdoe", "file:///prog1/les_fonctions/appeler_une_fonction/info.yml", $avancement])
			->andReturnArg(2);

		$interacteur = new SauvegarderAvancementInt();
		$résultat_observé = $interacteur->sauvegarder(
			"jdoe",
			"file:///prog1/les_fonctions/appeler_une_fonction/info.yml",
			$avancement,
		);

		$this->assertEquals($avancement, $résultat_observé);
	}
}
