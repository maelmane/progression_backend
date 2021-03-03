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

namespace progression\http\transformer;

use PHPUnit\Framework\TestCase;
use progression\domaine\entité\TentativeProg;
use progression\util\Encodage;

final class TentativeTransformerTests extends TestCase
{
	public function test_étant_donné_une_tentative_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$tentative = new TentativeProg(10, "codeTest", "dateSoumissionTest", "testsRéussisTest", "feedBackTest");
		$tentativeTransformer = new TentativeTransformer();

		$résultat = [
			'id' => $tentative->date_soumission,
			'date_soumission' => $tentative->date_soumission,
			'tests_réussis' => $tentative->tests_réussis,
			'feedback' => $tentative->feedback,
			'langage' => $tentative->langid,
			'code' => $tentative->code
		];

		$this->assertEquals($résultat, $tentativeTransformer->transform($tentative));
	}
}
