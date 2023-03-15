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

use progression\domaine\entité\{TestSys, Résultat, QuestionSys, TentativeSys};
use progression\dao\exécuteur\Exécuteur;
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

final class ExécuterSysIntTests extends TestCase
{
	protected static $questionTest;

	public function setUp(): void
	{
		parent::setUp();

		$_ENV["COMPILEBOX_URL"] = "file://" . __DIR__ . "/ExécuterSysIntTests_fichiers/test_exec_question_sys";
		$_SERVER["REMOTE_ADDR"] = "";
		$_SERVER["PHP_SELF"] = "";

		self::$questionTest = [
			new TestSys(
				nom: "nomTest",
				sortie_attendue: "sortieTest",
				validation: "validationTest",
				utilisateur: "utilisateurTest",
				feedback_pos: "feedbackPositif",
				feedback_neg: "feedbackNégatif",
			),
		];

		$mockExécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($utilisateur, $image, $conteneur, $tests) {
				return $utilisateur == "utilisateurTest" &&
					$image == "imageTest" &&
					$conteneur == null &&
					$tests == self::$questionTest;
			})
			->andReturn([
				"temps_exec" => 0.124,
				"résultats" => [["output" => "", "errors" => "", "time" => 0.2, "code" => 0]],
				"conteneur" => ["id" => "nouveauConteneur", "ip" => "172.45.2.2", "port" => 45667],
			]);

		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($utilisateur, $image, $conteneur, $tests) {
				return $utilisateur == "utilisateurTest" &&
					$image == "imageTest" &&
					$conteneur == "idConteneur" &&
					$tests == self::$questionTest;
			})
			->andReturn([
				"temps_exec" => 0.124,
				"résultats" => [["output" => "ok\n", "errors" => "", "time" => 0.2, "code" => 0]],
				"conteneur" => ["id" => "idConteneur", "ip" => "172.45.2.2", "port" => 45667],
			]);

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_exécuteur()
			->andReturn($mockExécuteur);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_une_question_avec_une_tentative_sans_conteneur_on_recoit_lid_dun_nouveau_conteneur()
	{
		$conteneur_attendu = ["id" => "nouveauConteneur", "ip" => "172.45.2.2", "port" => 45667];

		$exécuter_sys_int = new ExécuterSysInt();

		$résultat_observé = $exécuter_sys_int->exécuter("utilisateurTest", "imageTest", null, self::$questionTest);

		$this->assertEquals($conteneur_attendu, $résultat_observé["conteneur"]);
	}

	public function test_étant_donné_une_question_avec_une_tentative_avec_conteneur_on_recoit_lid_du_conteneur_et_le_résultat_dexécution()
	{
		$conteneur_attendu = ["id" => "idConteneur", "ip" => "172.45.2.2", "port" => 45667];
		$résultat_attendu = [
			"conteneur" => $conteneur_attendu,
			"temps_exécution" => 124,
			"résultats" => [
				new Résultat(sortie_observée: "ok\n", sortie_erreur: "", résultat: true, temps_exécution: 200),
			],
		];

		$exécuter_sys_int = new ExécuterSysInt();

		$résultat_observé = $exécuter_sys_int->exécuter(
			"utilisateurTest",
			"imageTest",
			["id" => "idConteneur"],
			self::$questionTest,
		);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
