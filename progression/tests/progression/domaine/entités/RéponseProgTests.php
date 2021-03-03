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

namespace progression\domaine\entité;

use PHPUnit\Framework\TestCase;

final class RéponseProgTests extends TestCase
{
	public function test_étant_donné_une_RéponseProg_instanciée_avec_numéro_2_lorsquon_récupère_son_numéro_on_obtient_2()
	{
		$RéponseProgTest = new RéponseProg(2);

		$numéro = $RéponseProgTest->numéro;

		$this->assertEquals(2, $numéro);
	}

	public function test_étant_donné_une_RéponseProg_instanciée_avec_sortie_observée_sortieObservéeTest_lorsquon_récupère_sa_sortie_observée_on_obtient_sortieObservéeTest()
	{
		$RéponseProgTest = new RéponseProg(2);
		$RéponseProgTest->sortie_observée = "sortieObservéeTest";

		$sortie_observée = $RéponseProgTest->sortie_observée;

		$this->assertEquals("sortieObservéeTest", $sortie_observée);
	}

	public function test_étant_donné_une_RéponseProg_instanciée_avec_sortie_erreur_sortieObservéeTest_lorsquon_récupère_sa_sortie_erreur_on_obtient_sortieObservéeTest()
	{
		$RéponseProgTest = new RéponseProg(2);
		$RéponseProgTest->sortie_erreur = "sortieObservéeTest";

		$sortie_erreur = $RéponseProgTest->sortie_erreur;

		$this->assertEquals("sortieObservéeTest", $sortie_erreur);
	}

	public function test_étant_donné_une_RéponseProg_instanciée_avec_résultat_résultatTest_lorsquon_récupère_son_résultat_on_obtient_résultatTest()
	{
		$RéponseProgTest = new RéponseProg(2);
		$RéponseProgTest->résultat = "résultatTest";

		$résultat = $RéponseProgTest->résultat;

		$this->assertEquals("résultatTest", $résultat);
	}

	public function test_étant_donné_une_RéponseProg_instanciée_avec_feedback_feedbackTest_lorsquon_récupère_son_feedback_on_obtient_feedbackTest()
	{
		$RéponseProgTest = new RéponseProg(2);
		$RéponseProgTest->feedback = "feedbackTest";

		$feedback = $RéponseProgTest->feedback;

		$this->assertEquals("feedbackTest", $feedback);
	}
}
