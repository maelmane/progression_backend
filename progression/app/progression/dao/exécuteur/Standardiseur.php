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

class Standardiseur
{
	function standardiser($code, $lang)
	{
		if ($lang == "python") {
			$beautifier_cmd = ["black", "-"];
		} elseif ($lang == "cpp") {
			$beautifier_cmd = ["clang-format", "-"];
		} elseif ($lang == "java") {
			$beautifier_cmd = ["clang-format", "-"];
		} elseif ($lang == "bash") {
			$beautifier_cmd = ["beautysh", "-"];
		} elseif ($lang == "javascript") {
			$beautifier_cmd = ["npx", "standard", "--fix", "-"];
		} elseif ($lang == "typescript") {
			$beautifier_cmd = ["npx", "ts-standard", "--fix", "--project", "/tmp/tsconfig.eslint.json", "-"];
		} else {
			Log::warning("Aucun beautifier trouvé pour $lang");
			return $code;
		}

		$descriptorspec = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
		];

		$proc = proc_open($beautifier_cmd, $descriptorspec, $pipes);
		if ($proc) {
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
		} else {
			return false;
		}
	}
}
