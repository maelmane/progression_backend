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
namespace progression\dao\exécuteur;

use progression\TestCase;

use progression\domaine\entité\{Exécutable, TestProg};
use Illuminate\Support\Facades\Cache;
use Mockery;

final class ExécuteurCacheTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$this->mock_exécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$this->mock_exécuteur->shouldReceive("exécuter_prog")->andReturn([
			"temps_exécution" => 12345,
			"résultats" => [["output" => "sortie exécutée", "errors" => ""]],
		]);
		$this->mock_standardiseur = Mockery::mock("progression\\dao\\exécuteur\\Standardiseur");
		$this->mock_standardiseur
			->shouldReceive("standardiser")
			->with("nouveau code", "python")
			->andReturn("code standardisé");
		$this->mock_standardiseur
			->shouldReceive("standardiser")
			->with("nouveau   code", "python")
			->andReturn("code standardisé");
		$this->mock_standardiseur
			->shouldReceive("standardiser")
			->with("nouveau code", "java")
			->andReturn("code standardisé");
	}

	public function tearDown(): void
	{
		parent::tearDown();
		Mockery::close();
	}

	public function test_étant_donné_une_cache_vide_lorsquon_exécute_un_nouveau_code_on_obtient_le_code_exécuté()
	{
		$exécutable = new Exécutable("nouveau code", "python");
		$test = [new TestProg("test", "sortie", "entrée", "param")];

		Cache::shouldReceive("has")
			->once()
			->with("e8032dd801819a71571c41b3c87f529a")
			->andReturn(false);
		Cache::shouldNotReceive("get");
		Cache::shouldReceive("put")
			->once()
			->with("e8032dd801819a71571c41b3c87f529a", [["output" => "sortie exécutée", "errors" => ""]]);
		$résultat = (new ExécuteurCache($this->mock_exécuteur, $this->mock_standardiseur))->exécuter_prog(
			$exécutable,
			$test,
		);

		$this->assertEquals(
			[
				"temps_exécution" => 12345,
				"résultats" => [["output" => "sortie exécutée", "errors" => ""]],
			],
			$résultat,
		);
	}

	public function test_étant_donné_une_cache_contenant_le_code_à_exécuter_lorsquon_exécute_le_même_code_avec_le_même_langage_les_mêmes_entrées_et_paramètres_on_obtient_le_code_en_cache()
	{
		$exécutable = new Exécutable("nouveau code", "python");
		$test = [new TestProg("test", "sortie", "entrée", "param")];

		Cache::shouldReceive("get")
			->once()
			->with("e8032dd801819a71571c41b3c87f529a")
			->andReturn([["output" => "sortie prise en cache", "errors" => ""]]);
		Cache::shouldReceive("has")
			->once()
			->with("e8032dd801819a71571c41b3c87f529a")
			->andReturn(true);
		Cache::shouldNotReceive("put");

		$résultat = (new ExécuteurCache($this->mock_exécuteur, $this->mock_standardiseur))->exécuter_prog(
			$exécutable,
			$test,
		);

		$this->assertEquals(
			[
				"temps_exec" => 0,
				"résultats" => [["output" => "sortie prise en cache", "errors" => ""]],
			],
			$résultat,
		);
	}

	public function test_étant_donné_une_cache_contenant_le_code_à_exécuter_vide_lorsquon_exécute_le_même_code_avec_le_même_langage_les_mêmes_entrées_et_paramètres_on_obtient_une_chaîne_vide()
	{
		$exécutable = new Exécutable("nouveau code", "python");
		$test = [new TestProg("test", "sortie", "entrée", "param")];

		Cache::shouldReceive("get")
			->once()
			->with("e8032dd801819a71571c41b3c87f529a")
			->andReturn([["output" => "", "errors" => ""]]);
		Cache::shouldReceive("has")
			->once()
			->with("e8032dd801819a71571c41b3c87f529a")
			->andReturn(true);
		Cache::shouldNotReceive("put");

		$résultat = (new ExécuteurCache($this->mock_exécuteur, $this->mock_standardiseur))->exécuter_prog(
			$exécutable,
			$test,
		);

		$this->assertEquals(
			[
				"temps_exec" => 0,
				"résultats" => [["output" => "", "errors" => ""]],
			],
			$résultat,
		);
	}

	public function test_étant_donné_une_cache_contenant_le_code_à_exécuter_lorsquon_exécute_le_même_code_avec_un_autre_langage_les_mêmes_entrées_et_paramètres_on_obtient_le_code_exécuté()
	{
		$exécutable = new Exécutable("nouveau code", "java");
		$test = [new TestProg("test", "sortie", "entrée", "param")];

		Cache::shouldReceive("has")
			->once()
			->with("8d7dd086fe94394520c14fe098159378")
			->andReturn(false);
		Cache::shouldNotReceive("get");
		Cache::shouldReceive("put")
			->once()
			->with("8d7dd086fe94394520c14fe098159378", [["output" => "sortie exécutée", "errors" => ""]]);

		$résultat = (new ExécuteurCache($this->mock_exécuteur, $this->mock_standardiseur))->exécuter_prog(
			$exécutable,
			$test,
		);

		$this->assertEquals(
			[
				"temps_exécution" => 12345,
				"résultats" => [["output" => "sortie exécutée", "errors" => ""]],
			],
			$résultat,
		);
	}

	public function test_étant_donné_une_cache_contenant_le_code_à_exécuter_lorsquon_exécute_le_même_code_avec_le_même_langage_dautres_entrées_et_les_mêmes_paramètres_on_obtient_le_code_exécuté()
	{
		$exécutable = new Exécutable("nouveau code", "python");
		$test = [new TestProg("test", "sortie", "entrée différente", "param")];

		Cache::shouldReceive("has")
			->once()
			->with("879f8745392494c38a966d01eab2a23e")
			->andReturn(false);
		Cache::shouldNotReceive("get");
		Cache::shouldReceive("put")
			->once()
			->with("879f8745392494c38a966d01eab2a23e", [["output" => "sortie exécutée", "errors" => ""]]);

		$résultat = (new ExécuteurCache($this->mock_exécuteur, $this->mock_standardiseur))->exécuter_prog(
			$exécutable,
			$test,
		);

		$this->assertEquals(
			[
				"temps_exécution" => 12345,
				"résultats" => [["output" => "sortie exécutée", "errors" => ""]],
			],
			$résultat,
		);
	}

	public function test_étant_donné_une_cache_contenant_le_code_à_exécuter_lorsquon_exécute_le_même_code_avec_le_même_langage_les_mêmes_entrées_et_dautres_paramètres_on_obtient_le_code_exécuté()
	{
		$exécutable = new Exécutable("nouveau code", "python");
		$test = [new TestProg("test", "sortie", "entrée", "autre param")];

		Cache::shouldReceive("has")
			->once()
			->with("78e4674804ee6f7955997243441507d8")
			->andReturn(false);
		Cache::shouldNotReceive("get");
		Cache::shouldReceive("put")
			->once()
			->with("78e4674804ee6f7955997243441507d8", [["output" => "sortie exécutée", "errors" => ""]]);

		$résultat = (new ExécuteurCache($this->mock_exécuteur, $this->mock_standardiseur))->exécuter_prog(
			$exécutable,
			$test,
		);

		$this->assertEquals(
			[
				"temps_exécution" => 12345,
				"résultats" => [["output" => "sortie exécutée", "errors" => ""]],
			],
			$résultat,
		);
	}

	public function test_étant_donné_une_cache_contenant_le_code_à_exécuter_lorsquon_exécute_un_code_équivalent_après_standardisation_avec_le_même_langage_les_mêmes_entrées_et_paramètres_on_obtient_le_code_en_cache()
	{
		$exécutable = new Exécutable("nouveau   code", "python");
		$test = [new TestProg("test", "sortie", "entrée", "param")];

		Cache::shouldReceive("has")
			->once()
			->with("e8032dd801819a71571c41b3c87f529a")
			->andReturn(true);
		Cache::shouldReceive("get")
			->once()
			->with("e8032dd801819a71571c41b3c87f529a")
			->andReturn([["output" => "sortie prise en cache", "errors" => ""]]);
		Cache::shouldNotReceive("put");

		$résultat = (new ExécuteurCache($this->mock_exécuteur, $this->mock_standardiseur))->exécuter_prog(
			$exécutable,
			$test,
		);

		$this->assertEquals(
			[
				"temps_exec" => 0,
				"résultats" => [["output" => "sortie prise en cache", "errors" => ""]],
			],
			$résultat,
		);
	}
}
