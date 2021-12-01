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

use progression\domaine\entité\{QuestionProg, Exécutable, Test};
use PHPUnit\Framework\TestCase;

final class QuestionDAOTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		DAOFactory::setInstance(null);

		$this->question = new QuestionProg();
		$this->question->titre = "Affichage répété";
		$this->question->description = "Exercice simple sur les itérations à nombre d'itérations fixe";
		$this->question->enonce =
			"Saisissez un nombre sur l'entrée standard puis faites afficher la phrase «Bonjour le monde!» autant de fois.";
		$this->question->auteur = "Albert Einstein";
		$this->question->licence = "poétique";
		$this->question->feedback_neg =
			"Pour tout savoir sur les itérations énumérées : [clique ici](http://unlien.com)";
		$this->question->feedback_pos = "Bravo! tu es prêt à passer à un type de boucles plus complexe";

		// Ébauches
		$this->question->exécutables = [];
		$this->question->exécutables["python"] = new Exécutable(
			"#+VISIBLE\nnb_répétitions = int( input() )\n\n#+TODO\n\nprint( \"Bonjour le monde\" )\n\n#-TODO\n\n#-VISIBLE\n",
			"python",
		);
		$this->question->exécutables["java"] = new Exécutable(
			"import java.util.Scanner;\n\npublic class exec {\n\n//+VISIBLE\n\npublic static void main(String[] args) {\n\n	Scanner input = new Scanner( System.in );\n		\n	nb_répétitions = input.nextInt();\n\n//+TODO\n\n	System.out.println( \"Bonjour le monde\" );\n\n//-TODO\n\n	}\n	\n//-VISIBLE\n\n}\n",
			"java",
		);

		// Tests
		$this->question->tests = [
			new Test(
				"10 fois",
				"Bonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\n",
				"10",
			),
			new Test("1 fois", "Bonjour le monde", "1"),
			new Test(
				"0 fois",
				"",
				"0",
				"",
				"Bien joué! 0 est aussi une entrée valable.",
				"N'oublie pas les cas limites, 0 est aussi une entrée valable!",
			),
			new Test("2 fois", "Bonjour\nBonjour\n", "2", "", "Bien joué!", "Rien à dire"),
		];
	}

	public function tearDown(): void
	{
		parent::tearDown();
	}

	public function test_get_question()
	{
		$résultat_obtenu = (new QuestionDAO())->get_question(
			"file://" . __DIR__ . "/démo/boucles/boucle_énumérée/info.yml",
			new ChargeurQuestion(),
		);
		$this->question->uri = "file://" . __DIR__ . "/démo/boucles/boucle_énumérée/info.yml";

		$this->assertEquals($this->question, $résultat_obtenu);
	}

	public function test_étant_donnée_un_fichier_info_vide_lorsquon_récupère_la_question_on_obtien_une_QuestionProg_avec_des_attributs_par_défaut()
	{
		$résultat_attendu = new QuestionProg();
		$résultat_attendu->exécutables = ["python" => new Exécutable("", "python")];
		$résultat_attendu->tests = [new Test("#1", "")];
		$résultat_attendu->uri = "file://" . __DIR__ . "/démo/défauts/info.yml";

		$résultat_obtenu = (new QuestionDAO())->get_question(
			"file://" . __DIR__ . "/démo/défauts/info.yml",
			new ChargeurQuestion(),
		);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
