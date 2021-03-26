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
use progression\domaine\entité\RésultatProg;

final class RésultatProgTransformerTests extends TestCase
{
	public function test_étant_donné_une_RéponseProg_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$_ENV["APP_URL"] = "https://example.com/";
		$résultatProgTransformer = new RésultatProgTransformer();

		$résultat = new RésultatProg("Bonjour\nBonjour\n", "", true, "Bon travail!");
		$résultat->numéro = 0;
		$résultat->id =
			"bob"
			. "/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24"
			. "/1614374490"
			. "/0";

		$réponse_attendue = [
			"id" => "bob/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490/0",
			"numéro" => 0,
			"sortie_observée" => "Bonjour\nBonjour\n",
			"sortie_erreur" => "",
			"résultat" => true,
			"feedback" => "Bon travail!",
			"links" => [
				"self" =>
				"https://example.com/resultat/bob/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490/0",
			],
		];

		$résponse_observée = $résultatProgTransformer->transform($résultat);

		$this->assertEquals($réponse_attendue, $résponse_observée);
	}
}
