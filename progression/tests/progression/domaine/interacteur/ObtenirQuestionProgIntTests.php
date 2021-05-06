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

use progression\domaine\entité\{Question, QuestionProg};
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

final class ObtenirQuestionProgIntTests extends TestCase
{
	public function setUp(): void
	{
		$question = new QuestionProg();
		$question->uri = "prog1/les_fonctions/appeler_une_fonction";

		$mockQuestionDao = Mockery::mock("progression\dao\QuestionDAO");
		$mockQuestionDao
			->shouldReceive("get_type")
			->with("prog1/les_fonctions/appeler_une_fonction")
			->andReturn(Question::TYPE_PROG);
		$mockQuestionDao
			->shouldReceive("get_type")
			->with(Mockery::any())
			->andReturn(null);

		$mockQuestiongDao = Mockery::mock("progression\dao\QuestionDAO");
		$mockQuestionDao
			->shouldReceive("get_question")
			->with("prog1/les_fonctions/appeler_une_fonction")
			->andReturn($question);
		$mockQuestionDao
			->shouldReceive("get_question")
			->with(Mockery::any())
			->andReturn(null);

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_question_dao()
			->andReturn($mockQuestionDao);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_une_questionprog_avec_un_chemin_existant_lorsque_cherché_par_chemin_on_obtient_un_objet_questionprog_correspondant()
	{
		$interacteur = new ObtenirQuestionInt();
		$résultat_obtenu = $interacteur->get_question("prog1/les_fonctions/appeler_une_fonction");

		$résultat_attendu = new QuestionProg();
		$résultat_attendu->uri = "prog1/les_fonctions/appeler_une_fonction";

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_questionprog_avec_un_chemin_inexistant_lorsque_cherché_par_chemin_on_obtient_null()
	{
		$interacteur = new ObtenirQuestionInt();
		$résultat_obtenu = $interacteur->get_question("test/de/chemin/non/valide");

		$this->assertNull($résultat_obtenu);
	}
}