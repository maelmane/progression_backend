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

use progression\domaine\entité\{Exécutable, TestProg, Résultat};

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExécuteurCache extends Exécuteur
{
	private $_exécuteur;
	private $_standardiseur;

	public function __construct($exécuteur, $standardiseur)
	{
		$this->_exécuteur = $exécuteur;
		$this->_standardiseur = $standardiseur;
	}
	/**
	 * @param Exécutable $exécutable
	 * @param array<TestProg> $tests
	 *
	 * @return array<mixed> Un tableau de "résultats"=>array<id, Résultat> et "temps_exécution"=>int
	 */
	public function exécuter_prog($exécutable, $tests)
	{
		$code_standardisé = $this->standardiser_code($exécutable->code, $exécutable->lang) ?? $exécutable->code;

		$résultats = $this->obtenir_résultats($code_standardisé, $exécutable, $tests);

		return $résultats;
	}

	/**
	 * @param string $utilisateur
	 * @param string $image
	 * @param string $conteneur_id
	 * @param string $init
	 * @param array<TestProg> $tests
	 *
	 * @return array<mixed> Un tableau de "résultats"=>array<id, Résultat> et "temps_exécution"=>int
	 */
	public function exécuter_sys(
		string|null $utilisateur,
		string $image,
		string|null $conteneur_id,
		string|null $init,
		array $tests,
		int|null $test_index,
		string|null $commande,
	): array {
		return $this->_exécuteur->exécuter_sys(
			$utilisateur,
			$image,
			$conteneur_id,
			$init,
			$tests,
			$test_index,
			$commande,
		);
	}

	/**
	 * @return array<mixed>
	 */
	public function terminer(string $conteneur_id): array
	{
		return $this->_exécuteur->terminer($conteneur_id);
	}

	/**
	 * @param array<TestProg> $tests
	 *
	 * @return array<mixed> Un tableau de "résultats"=>array<id, Résultat> et "temps_exécution"=>int
	 */

	private function obtenir_résultats(string $code_standardisé, Exécutable $exécutable, array $tests): array
	{
		$résultats = [];
		$tests_à_exécuter = [];
		foreach ($tests as $test) {
			$entrée = $test->entrée;
			$params = $test->params;

			$hash = $this->calculer_hash($code_standardisé, $exécutable->lang, $entrée, $params);
			Log::debug("Hash: $hash");

			$résultat = false;
			try {
				$résultat = $this->obtenir_de_la_cache($hash);
			} catch (\Throwable $e) {
				Log::error("Cache non disponible");
				Log::error($e->getMessage());
			}

			if ($résultat !== false) {
				$résultats[$hash] = $résultat;
			} else {
				// Si le Résultat n'est pas trouvé dans la cache, on «réserve» l'espace dans le tableau
				// et on conserve le test pour exécution
				$résultats[$hash] = false;
				$tests_à_exécuter[$hash] = $test;
			}
		}

		// Remplit le tableau $résultats avec les Résultats de tests non présents dans la cache
		$temps_exécution = $this->exécuter_tests_manquants($tests_à_exécuter, $exécutable, $résultats);
		return ["résultats" => $résultats, "temps_exécution" => $temps_exécution];
	}

	/**
	 * @param array<TestProg> $tests_à_exécuter
	 * @param array<Résultat> $résultats Tableau de Résultats à remplir des résultats manquants
	 *
	 * @return int le temps d'exécution des tests manquants
	 */
	private function exécuter_tests_manquants(array $tests_à_exécuter, Exécutable $exécutable, array &$résultats): int
	{
		if ($tests_à_exécuter) {
			$résultats_exécution = $this->exécuter_tests($exécutable, $tests_à_exécuter);
			foreach ($résultats_exécution["résultats"] as $hash => $résultat) {
				$résultats[$hash] = $résultat;
			}
			return $résultats_exécution["temps_exécution"];
		} else {
			return 0;
		}
	}

	/**
	 * Exécute en lot de tests
	 *
	 * @param array<TestProg> $tests
	 *
	 * @return array<mixed> Un tableau de "résultats"=>array<id, Résultat> et "temps_exécution"=>int
	 */
	private function exécuter_tests(Exécutable $exécutable, array $tests): array
	{
		$exécution = $this->_exécuteur->exécuter_prog($exécutable, $tests);

		$réponses = $exécution["résultats"];

		$résultats["temps_exécution"] = $exécution["temps_exécution"];
		$résultats["résultats"] = [];
		foreach ($réponses as $réponse) {
			$hash = key($tests);
			$résultats["résultats"][$hash] = $réponse;
			if (!$this->contient_des_erreurs($réponse)) {
				try {
					$this->placer_sortie_en_cache($hash, $réponse);
				} catch (\Throwable $e) {
					Log::error("Cache non disponible");
					Log::error($e->getMessage());
				}
			}

			next($tests);
		}

		return $résultats;
	}

	private function calculer_hash($code, $lang, $entrée, $params)
	{
		return md5($code . $lang . $entrée . $params);
	}

	private function standardiser_code($code, $lang)
	{
		return $this->_standardiseur->standardiser($code, $lang);
	}

	private function obtenir_de_la_cache($hash)
	{
		if (!Cache::has($hash)) {
			Log::debug("Cache : Miss");
			return false;
		}

		Log::debug("Cache : Hit");
		return Cache::get($hash);
	}

	private function placer_sortie_en_cache($hash, $résultat)
	{
		return Cache::put($hash, $résultat);
	}

	private function contient_des_erreurs($résultat)
	{
		return $résultat["errors"];
	}
}
