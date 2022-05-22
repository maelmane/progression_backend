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

final class TentativeSysTests extends TestCase
{
	public function test_étant_donné_une_TentativeSys_instanciée_avec_tous_ses_paramètres_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$résultats_attendu = ["résultat1", "résultat2"];
		$commentaires_attendu = ["commentaire1", "commentaire2"];

		$résultats = ["résultat1", "résultat2"];
		$commentaires = ["commentaire1", "commentaire2"];

		$tentativeSysTest = new TentativeSys(
			"conteneurTest",
			"reponseTest",
			3456,
			true,
			2,
			100,
			"Bravo!",
			$résultats,
			$commentaires,
		);

		$this->assertEquals("conteneurTest", $tentativeSysTest->conteneur);
		$this->assertEquals("reponseTest", $tentativeSysTest->réponse);
		$this->assertEquals(3456, $tentativeSysTest->date_soumission);
		$this->assertTrue($tentativeSysTest->réussi);
		$this->assertEquals(100, $tentativeSysTest->temps_exécution);
		$this->assertEquals(2, $tentativeSysTest->tests_réussis);
		$this->assertEquals("Bravo!", $tentativeSysTest->feedback);
		$this->assertEquals($résultats_attendu, $tentativeSysTest->résultats);
		$this->assertEquals($commentaires_attendu, $tentativeSysTest->commentaires);
	}
}
