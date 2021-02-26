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
use progression\domaine\entité\Test;
use PHPUnit\Framework\TestCase;

final class TestTransformerTests extends TestCase
{
	public function test_étant_donné_un_test_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$testTransformer = new TestTransformer();
		$test = new Test("Somme de deux nombres", "21\n21\n", "42");
		$test->id =
			"cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/0";
		$test->numéro = 0;
		$résultat_attendu = [
			"id" =>
				"cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/0",
			"numéro" => 0,
			"nom" => "Somme de deux nombres",
			"entrée" => "21\n21\n",
			"sortie_attendue" => "42",
			"links" => [
				"self" =>
					"https://example.com/test/cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/0",
			],
		];
		$résultat_obtenu = $testTransformer->transform($test);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}

?>
