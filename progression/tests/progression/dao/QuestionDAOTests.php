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
        $question->titre = "Affichage répété";
        $question->description = "Exercice simple sur les itérations à nombre d'itérations fixe";
        $question->enonce = "Saisissez un nombre sur l'entrée standard puis faites afficher la phrase «Bonjour le monde!» autant de fois.";
        $question->feedback_neg = "Pour tout savoir sur les itérations énumérées : [clique ici](http://unlien.com)";
        $question->feedback_pos = "Bravo! tu es prêt à passer à un type de boucles plus complexe";
        
        // Ébauches
        $question->exécutables = [];
        $question->exécutables["python"] = new Exécutable("#+VISIBLE\nnb_répétitions = int( input() )\n\n#+TODO\n\nprint( \"Bonjour le monde\" )\n\n#-TODO\n\n#-VISIBLE\n", "python");
        $question->exécutables["java"] = new Exécutable("import java.util.Scanner;\n\npublic class exec {\n\n//+VISIBLE\n\npublic static void main(String[] args) {\n\n	Scanner input = new Scanner( System.in );\n		\n	nb_répétitions = input.nextInt();\n\n//+TODO\n\n	System.out.println( \"Bonjour le monde\" );\n\n//-TODO\n\n	}\n	\n//-VISIBLE\n\n}\n", "java");

        // Tests
        $question->tests = [
            new Test("10 fois",
                     "10",
                     "Bonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\n"),
            new Test("1 fois",
                     "1",
                     "Bonjour le monde"),
            new Test("0 fois",
                     "0",
                     "",
                     "",
                     "Bien joué! 0 est aussi une entrée valable.",
                     "N'oublie pas les cas limites, 0 est aussi une entrée valable!"),
            new Test("2 fois",
                     "2",
                     "Bonjour\nBonjour\n",
                     "",
                     "Bien joué!",
                     "Rien à dire"),
        ];

		$résultat_obtenu = (new QuestionDAO())->get_question(
			"file:///var/www/progression/tests/progression/dao/démo/boucles/boucle_énumérée"
		);

        $this->assertEquals($question, $résultat_obtenu);

	}
}
