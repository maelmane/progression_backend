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
use progression\domaine\entité\Résultat;

final class RésultatTransformerTests extends TestCase
{
	public function test_étant_donné_un_Résultat_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$résultat = new Résultat("Bonjour\nBonjour\n", "", true, "Bon travail!", 15);
		$résultat->id = "0";

		$réponse_attendue = [
			"id" => "0",
			"sortie_observée" => "Bonjour\nBonjour\n",
			"sortie_erreur" => "",
			"résultat" => true,
			"feedback" => "Bon travail!",
			"temps_exec" => 15,
			"links" => [
				"self" => "https://example.com/resultat/0",
			],
		];

		$résultatProgTransformer = new RésultatTransformer();
		$résponse_observée = $résultatProgTransformer->transform($résultat);

		$this->assertEquals($réponse_attendue, $résponse_observée);
	}
}
