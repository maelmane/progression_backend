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

	public function __construct($exécuteur)
	{
		$this->_exécuteur = $exécuteur;
	}

	public function exécuter($exécutable, $test)
	{
		$hash = $this->calculer_hash($exécutable->code, $exécutable->lang, $test->entrée);
		Log::debug("Hash : $hash");

		$résultat = $this->obtenir_de_la_cache($hash);

		if ($résultat) {
			Log::debug("Cache : Hit");
			return $résultat;
		}
		Log::debug("Cache : Miss");

		$hash_non_formaté = $hash;

		$code_standardisé = $this->standardiser_code($exécutable->code, $exécutable->lang);
		$hash = $this->calculer_hash($code_standardisé, $exécutable->lang, $test->entrée);
		Log::debug("Hash formaté: $hash");

		$résultat = $this->obtenir_de_la_cache($hash);

		if ($résultat) {
			Log::debug("Cache : Hit");
		} else {
			Log::debug("Cache : Miss");
			$résultat = $this->_exécuteur->exécuter($exécutable, $test);

			if (!$this->contient_des_erreurs($résultat)) {
				$this->placer_en_cache($hash, $résultat);
				$this->placer_en_cache($hash_non_formaté, $résultat);
			}
		}

		return $résultat;
	}

	private function calculer_hash($code, $lang, $entrée)
	{
		return md5($code . $lang . $entrée);
	}

	private function standardiser_code($code, $lang)
	{
		if ($lang == "python") {
			$beautifier_cmd = ["black", "-"];
		} elseif ($lang == "cpp") {
			$beautifier_cmd = ["clang-format", "-"];
		} elseif ($lang == "java") {
			$beautifier_cmd = ["clang-format", "-"];
		} elseif ($lang == "bash") {
			$beautifier_cmd = ["beautysh", "-"];
		} else {
			return $code;
		}

		$descriptorspec = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
		];

		$proc = proc_open($beautifier_cmd, $descriptorspec, $pipes);

		$stdout = "";
		if (is_resource($proc)) {
			fwrite($pipes[0], $code);
			fclose($pipes[0]);

			$stdout = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
		}

		$retour = proc_close($proc);
		if ($retour != 0) {
			Log::error("Beautifier erreur code $retour");
		} else {
			Log::debug("Code formaté : $stdout");
		}

		return $retour == 0 ? $stdout : $code;
	}

	private function obtenir_de_la_cache($hash)
	{
		return Cache::get($hash);
	}

	private function placer_en_cache($hash, $résultat)
	{
		return Cache::put($hash, $résultat);
	}

	private function contient_des_erreurs($résultat)
	{
		return json_decode($résultat, true)["errors"];
	}
}
