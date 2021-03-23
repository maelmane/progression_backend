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

use progression\domaine\entité\{Exécutable, Avancement, QuestionProg, RésultatProg, TentativeProg, Test};
use PHPUnit\Framework\TestCase;
use \Mockery;

final class SoumettreTentativeProgIntTests extends TestCase
{
	public function test_étant_donné_une_questionprog_et_une_tentativeprog_lorsqu_on_appelle_soumettre_tentative_on_obtient_un_objet_tentative_comportant_les_tests_réussis_et_les_résultats()
	{
		$résultat_attendu = new TentativeProg(
			"python",
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			1615696286,
			false,
			0,
			"feedbackTentativeTest",
		);
		$résultat_attendu->résultats = [
			new RésultatProg(
				"sortieTest",
				"erreurTest",
				false,
				"feedbackRésultatTest",
			),
		];

		$test = new Test(
			"nomTest",
			"entréeTest",
			"sortieTest",
		);

		$question = new QuestionProg();
		$question->question_uri =
			"https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour";
		$question->tests = [
			$test,
		];
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			"python",
		);

		$tentative = new TentativeProg(
			"python",
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			1615696286,
			false,
			0,
			"feedbackTentativeTest",
		);

		$exécutable = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			"python",
		);
		$résultat = new RésultatProg(
			"sortieTest",
			"erreurTest",
			false,
			"feedbackRésultatTest",
		);

		// Mock interacteurs
		$mockPréparerProgInt = Mockery::mock(
			"progression\domaine\interacteur\PréparerProgInt"
		);
		$mockPréparerProgInt
			->allows()
			->préparer_exécutable(
				$question,
				$tentative,
			)
			->andReturn(
				$exécutable,
			);

		$mockTraiterTentativeProgInt = Mockery::mock(
			"progression\domaine\interacteur\TraiterTentativeProgInt"
		);
		$mockTraiterTentativeProgInt
			->allows()
			->traiter_résultats(
				$tentative->résultats,
				$question->tests,
				"jdoe",
			)
			->andReturn(
				[
					$résultat,
				]
			);
		$mockExécuterProgInt = Mockery::mock(
			"progression\domaine\interacteur\ExécuterProgInt"
		);
		$mockExécuterProgInt
			->allows()
			->exécuter(
				$exécutable,
				$test,
			)
			->andReturn(
				$résultat,
			);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock(
			"progression\domaine\interacteur\InteracteurFactory"
		);
		$mockIntFactory
			->allows()
			->getPréparerProgInt()
			->andReturn($mockPréparerProgInt);
		$mockIntFactory
			->allows()
			->getTraiterTentativeProgInt()
			->andReturn($mockTraiterTentativeProgInt);
		$mockIntFactory
			->allows()
			->getExécuterProgInt()
			->andReturn($mockExécuterProgInt);

		$interacteur = new SoumettreTentativeProgInt($mockIntFactory);
		$interacteur->intFactory = $mockIntFactory;
		$résultat_obtenu = $interacteur->soumettre_tentative(
			"jdoe",
			$question,
			$tentative,
		);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_questionprog_et_une_tentativeprog_lorsqu_on_appelle_soumettre_tentative_on_obtient_null()
	{
		$question = new QuestionProg();
		$question->question_uri =
			"https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour";
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			"python",
		);
		$tentative = new TentativeProg("java", "sans importance");
		$exécutable = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			"python",
		);

		// Mock interacteurs
		$mockPréparerProgInt = Mockery::mock(
			"progression\domaine\interacteur\PréparerProgInt"
		);
		$mockPréparerProgInt
			->allows()
			->préparer_exécutable(
				$question,
				$tentative,
			)
			->andReturn($exécutable);


		$interacteur = new PréparerProgInt();

		$résultat_obtenu = $interacteur->préparer_exécutable($question, $tentative);

		$this->assertNull($résultat_obtenu);
	}
}
