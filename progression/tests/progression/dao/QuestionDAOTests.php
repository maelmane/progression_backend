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

require_once __DIR__ . "/../../TestCase.php";
use progression\domaine\entité\{Question, QuestionProg, Exécutable, Test};

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class QuestionCtlTests extends \TestCase
{
	public function test_get_question()
	{
        $question = new QuestionProg();
        $question->type = Question::TYPE_PROG;
        $question->uri = "file:///var/www/progression/tests/progression/dao/démo/boucles/boucle_énumérée";
        $question->titre = "Appeler une fonction paramétrée";
        $question->description =
            "Appel d'une fonction existante recevant un paramètre";
        $question->enonce =
            "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";

        // Ébauches
        $question->exécutables = [
            new Exécutable("print(\"Hello world\")", "python"),
            new Exécutable("System.out.println(\"Hello world\")", "java"),
        ];

        // Tests
        $question->tests = [
            new Test("2 salutations", "2", "Bonjour\nBonjour\n"),
            new Test("Aucune salutation", "0", ""),
        ];

		$résultat_obtenu = (new QuestionDAO())->get_question(
			"file:///var/www/progression/tests/progression/dao/démo/boucles/boucle_énumérée/"
		);

        $this->assertEquals($question, $résultat_obtenu);

	}
}
