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

use progression\domaine\entité\{Exécutable, QuestionProg, TentativeProg};
use PHPUnit\Framework\TestCase;
use Mockery;

final class PréparerProgIntTests extends TestCase
{
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_une_questionprog_sans_todo_et_une_tentative_lorsque_préparé_on_obtient_objet_exécutable_comportant_le_exactement_le_code_utilisateur()
	{
		$résultat_attendu = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE

             print(\"Allo le monde\")

             print(\"Test 123\")",

			"python",
		);

		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE

             print()

             # Rien à faire ici

             # À faire
            ",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#Commentaire invisible
             #+VISIBLE

             print(\"Allo le monde\")

             print(\"Test 123\")",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_questionprog_à_un_todo_sans_balise_de_début_et_une_tentative_lorsque_préparé_on_obtient_objet_exécutable_comportant_le_seulement_code_utilisateur_avant_le_todo()
	{
		$résultat_attendu = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE

             print(\"Allo le monde\")
             #-TODO
             # Rien à faire ici

            ",
			"python",
		);

		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE

             print()
             #-TODO
             # Rien à faire ici

            ",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#Commentaire invisible
             #+VISIBLE

             print(\"Allo le monde\")
             #-TODO
             # Ne devrait pas être ici

            ",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_questionprog_à_un_todo_sans_balise_de_fin_et_une_tentative_lorsque_préparé_on_obtient_objet_exécutable_comportant_le_seulement_code_utilisateur_après_le_todo()
	{
		$résultat_attendu = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE

             # Rien à faire ici

             #+TODO
             print(\"Allo le monde\")

            ",
			"python",
		);

		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE

             # Rien à faire ici

             #+TODO
             print()

            ",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#Commentaire invisible
             #+VISIBLE

             # Ne devrait pas être ici

             #+TODO
             print(\"Allo le monde\")

            ",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_questionprog_à_deux_todos_et_une_tentative_lorsque_préparé_on_obtient_objet_exécutable_comportant_le_seulement_code_utilisateur_entre_todos()
	{
		$résultat_attendu = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE
             #+TODO
             print(\"Allo le monde\")
             #-TODO
             # Rien à faire ici
             #+TODO
             
             print(\"Test 123\")",

			"python",
		);

		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE
             #+TODO
             print()
             #-TODO
             # Rien à faire ici
             #+TODO
             # À faire
            ",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#Commentaire invisible
             #+VISIBLE
             # Ne devrait pas être ici
             #+TODO
             print(\"Allo le monde\")
             #-TODO
             # Ne devrait pas être ici
             #+TODO
             
             print(\"Test 123\")",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_question_et_une_tentative_pour_un_langage_sans_ébauche_lorsquon_prépare_lexécutable_on_obtient_null()
	{
		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE
             #+TODO
             print()
             #-TODO
             # Rien à faire ici
             #+TODO
             # À faire
            ",
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
			"#Commentaire invisible
             #+VISIBLE
             #+TODO
             print()
             #-TODO
             # Rien à faire ici
             #+TODO
             # À faire
            ",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#+TODO
             print(1)
             #-TODO
            ",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_une_ébauche_avec_1_todo_et_tentative_avec_2_todo_lorsquon_prépare_lexécutable_on_obtient_null()
	{
		$question = new QuestionProg();
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible
             #+VISIBLE
             #+TODO
             print()
             #-TODO
            ",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#+TODO
             print(1)
             #-TODO
             #+TODO
             print(1)
             #-TODO
            ",
		);

		$interacteur = new PréparerProgInt();
		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertNull($résultat_obtenu);
	}
}
