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

	public function exécuter($exécutable, $test)
	{
		$code_standardisé = $this->standardiser_code($exécutable->code, $exécutable->lang) ?? $exécutable->code;

		$hash = $this->calculer_hash($code_standardisé, $exécutable->lang, $test->entrée, $test->params);
		Log::debug("Hash: $hash");

		$résultat = $this->obtenir_de_la_cache($hash);

		if ($résultat == null) {
			$résultat = $this->_exécuteur->exécuter($exécutable, $test);

			if (!$this->contient_des_erreurs($résultat)) {
				$this->placer_sortie_en_cache($hash, $résultat);
			}
		}

		return $résultat;
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
			return null;
		}
		
		Log::debug("Cache : Hit");
		return json_encode(["output" => Cache::get($hash), "errors" => null]);

	}

	private function placer_sortie_en_cache($hash, $résultat)
	{
		return Cache::put($hash, json_decode($résultat, true)["output"]);
	}

	private function contient_des_erreurs($résultat)
	{
		return json_decode($résultat, true)["errors"];
	}
}
