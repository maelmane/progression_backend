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

require_once __DIR__ . '/../../../TestCase.php';

use progression\domaine\entité\{Question, QuestionProg, Exécutable};
use progression\http\contrôleur\ÉbaucheCtl;
use progression\dao\DAOFactory;
use Illuminate\Http\Request;

final class ÉbaucheCtlTests extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->chemin = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		// Ébauches
		$question->exécutables["python"] = new Exécutable("print(\"Hello world\")", "python");
		$question->exécutables["java"] = new Exécutable("System.out.println(\"Hello world\")", "java");

		$mockQuestionDAO = Mockery::mock("progression\dao\QuestionDAO");
		$mockQuestionDAO
		->shouldReceive("get_question")
		->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
		->andReturn($question);
		
		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->shouldReceive("get_question_dao")
			->andReturn($mockQuestionDAO);
		DAOFactory::setInstance($mockDAOFactory);
    }

    public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_le_chemin_dune_ébauche_lorsquon_appelle_get_on_obtient_lébauche_et_ses_relations_sous_forme_json()
	{

        $résultat_obtenu = $this->call(
            "GET",
			"/ebauche/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/python"
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertStringEqualsFile(__DIR__ . "/ébaucheCtlTests_1.json", $résultat_obtenu->getContent());
	}

	public function test_étant_donné_le_chemin_dune_ébauche_inexistante_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
        $résultat_obtenu = $this->call(
            "GET",
			"/ebauche/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/fortran"
		);

		$this->assertEquals(404, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_obtenu->getContent());
	}
}
