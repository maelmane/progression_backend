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

use progression\domaine\entité\{Exécutable, Test, RésultatProg};
use progression\dao\exécuteur\Exécuteur;
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

final class ExécuterProgIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$_ENV["COMPILEBOX_URL"] = "file://" . __DIR__ . "/ExécuterProgIntTests_fichiers/test_exec_prog_int_python";
		$_SERVER["REMOTE_ADDR"] = "";
		$_SERVER["PHP_SELF"] = "";

		$mockExécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$mockExécuteur
			->shouldReceive("exécuter")
			->with(
				Mockery::on(function ($param) {
					return $param == new Exécutable("a=int(input())\nfor i in range(a):print('ok')", "python");
				}),
				Mockery::on(function ($param) {
					return $param == new Test("premier test", "ok\n", "1");
				}),
			)
			->andReturn("{\"output\": \"ok\", \"errors\":\"\"}");

		$mockExécuteur
			->shouldReceive("exécuter")
			->with(
				Mockery::on(function ($param) {
					return $param == new Exécutable("a=a", "python");
				}),
				Mockery::on(function ($param) {
					return $param == new Test("premier test", "ok\n", "1");
				}),
			)
			->andReturn("{\"output\": \"\", \"errors\":\"erreur\"}");

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

	public function test_étant_donné_un_exécutable_valide_et_un_test_lorsquon_les_soumet_pour_exécution_on_obtient_un_résultat_de_test_avec_ses_sorties_standards()
	{
		$exécutable_valide = new Exécutable("a=int(input())\nfor i in range(a):print('ok')", "python");
		$test = new Test("premier test", "ok\n", "1");

		$résultat_observé = (new ExécuterProgInt())->exécuter($exécutable_valide, $test);

		$résultat_attendu = new RésultatProg("ok", "");
		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_un_exécutable_d_erreur_et_un_test_lorsquon_les_soumet_pour_exécution_on_obtient_un_résultat_de_test_avec_ses_sorties_d_erreur()
	{
		$exécutable_erreur = new Exécutable("a=a", "python");
		$test = new Test("premier test", "ok\n", "1");

		$résultat_observé = (new ExécuterProgInt())->exécuter($exécutable_erreur, $test);

		$résultat_attendu = new RésultatProg("", "erreur");
		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
