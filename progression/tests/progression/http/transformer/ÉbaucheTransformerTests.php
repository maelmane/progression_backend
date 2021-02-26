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

use progression\domaine\entité\Exécutable;
use PHPUnit\Framework\TestCase;

final class ÉbaucheTransformerTests extends TestCase
{
	public function test_étant_donné_une_ébauche_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$_ENV["APP_URL"] = "https://example.com/";
		$ébaucheTransformer = new ÉbaucheTransformer();

		$exécutable = new Exécutable("return nb1 + nb2;", "java");
		$exécutable->id = "cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/java";

		$résultat_attendu = [
			"id" => "cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/java",
			"langage" => "java",
			"code" => "return nb1 + nb2;",
			"links" => [
				"self" =>
				"https://example.com/ebauche/cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/java",
			],
		];
		$résultat_obtenu = $ébaucheTransformer->transform($exécutable);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
