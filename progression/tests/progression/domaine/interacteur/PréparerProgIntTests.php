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

use progression\domaine\entité\{Exécutable, AvancementProg, QuestionProg, RéponseProg, TentativeProg};
use PHPUnit\Framework\TestCase;
use \Mockery;

final class PréparerProgIntTests extends TestCase
{
	public function test_étant_donné_une_questionprog_avancementprog_langage_et_code__lorsque_cherché_par_questionprog_avancementprog_langage_et_code_on_obtient_un_objet_exécutable_correspondant()
	{
		$résultat_attendu = new Exécutable("+TODO\nprint()\n-TODO", "python");

		$question = new QuestionProg();
		$question->question_uri = "https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour";
		$question->exécutables["python"] = new Exécutable("+TODO\nprint()\n-TODO", "python");

		$tentative = new TentativeProg(
			"python",
			"+TODO\nprint()\n-TODO",
			1614965817
		);
		$tentative->résultats = [
			new RéponseProg("python", "+TODO\nprint()\n-TODO", 1614965817)
		];

		$avancement = new AvancementProg(
			"https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour",
			"bob"
		);
		$avancement->résultats = [$tentative];

		$interacteur = new PréparerProgInt();

		$résultat_obtenu = $interacteur->préparer_exécutable(
			$question,
			$avancement,
			"python",
			"+TODO\nprint()\n-TODO"
		);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
