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

use progression\domaine\entité\{Exécutable, Avancement, QuestionProg, TentativeProg};
use PHPUnit\Framework\TestCase;
use \Mockery;

final class PréparerProgIntTests extends TestCase
{
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_une_questionprog_et_une_tentative_lorsque_préparé_on_obtient_objet_exécutable_comportant_le_seulement_code_utilisateur_entre_todos()
	{
		$résultat_attendu = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint(\"Allo le monde\")\n#-TODO\n# Rien à faire ici\n#+TODO\n\nprint(\"Test 123\")",
			"python",
		);

		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#Commentaire invisible\n#+VISIBLE\n#Ne devrait pas être ici\n#+TODO\nprint(\"Allo le monde\")\n#-TODO\n# Rien à faire ici\n#+TODO\n\nprint(\"Test 123\")",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_question_et_une_tentative_pour_un_langage_sans_ébauche_lorsquon_prépare_lexécutable_on_obtient_null()
	{
		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			"python",
		);

		$tentative = new TentativeProg("java", "sans importance");

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_une_ébauche_avec_2_todo_et_tentative_avec_1_todo_lorsquon_prépare_lexécutable_on_obtient_null()
	{
		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#+TODO\nprint(1)\n#-TODO",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_une_ébauche_avec_1_todo_et_tentative_avec_2_todo_lorsquon_prépare_lexécutable_on_obtient_null()
	{
		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#+TODO\nprint(1)\n#-TODO\n#+TODO\nprint(1)\n#-TODO",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertNull($résultat_obtenu);
	}
}
