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

	public function exécuter_prog($exécutable, $tests)
	{
		$code_standardisé = $this->standardiser_code($exécutable->code, $exécutable->lang) ?? $exécutable->code;
		$réponse = [];
		$réponse["résultats"] = [];
		$tests_à_exécuter = [];
		foreach ($tests as $test) {
			$entrée = $test->entrée;
			$params = $test->params;

			$hash = $this->calculer_hash($code_standardisé, $exécutable->lang, $entrée, $params);
			Log::debug("Hash: $hash");

			$résultats = null;
			try {
				$résultats = $this->obtenir_de_la_cache($hash);
			} catch (\Throwable $e) {
				Log::error("Cache non disponible");
				Log::error($e->getMessage());
			}

			if ($résultats !== false) {
				$réponse["résultats"][$hash] = $résultats;
			} else {
				$tests_à_exécuter[$hash] = $test;
				$réponse["résultats"][$hash] = null;
			}
		}

		if (count($tests_à_exécuter) == 0) {
			$réponse["temps_exécution"] = 0;
		} else {
			$résultats_exécution = $this->exécuter_tests($exécutable, $tests_à_exécuter);
			foreach ($résultats_exécution["résultats"] as $hash => $résultat) {
				$réponse["résultats"][$hash] = $résultat;
			}
			$réponse["temps_exécution"] = $résultats_exécution["temps_exécution"];
		}

		return $réponse;
	}

	private function exécuter_tests($exécutable, $tests)
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
