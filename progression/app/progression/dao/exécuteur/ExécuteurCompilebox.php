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

class ExécuteurCompilebox extends Exécuteur
{
	const langages = [
		"python2" => 0,
		"python" => 1,
		"ruby" => 2,
		"clojure" => 3,
		"php" => 4,
		"javascript" => 5,
		"scala" => 6,
		"go" => 7,
		"cpp" => 8,
		"c" => 9,
		"java" => 10,
		"bash" => 11,
		"perl" => 12,
		"sshd" => 13,
		"mysql" => 14,
		"powershell" => 15,
		"typescript" => 16,
	];

	public function exécuter($exécutable, $tests)
	{
		//post le code à remotecompiler
		$url_rc = $_ENV["COMPILEBOX_URL"];

		$tests_out = [];
		foreach ($tests as $test) {
			$tests_out[] = ["stdin" => $test->entrée ?? "", "params" => $test->params ?? ""];
		}

		$data_rc = [
			"language" => self::langages[$exécutable->lang],
			"code" => $exécutable->code,
			"parameters" => "",
			"tests" => $tests_out,
			"vm_name" => "remotecompiler",
		];

		$options_rc = [
			"http" => [
				"header" => "Content-type: application/x-www-form-urlencoded\r\n",
				"method" => "POST",
				"content" => http_build_query($data_rc),
			],
		];
		$context = stream_context_create($options_rc);

		try {
			$comp_resp = file_get_contents($url_rc, false, $context);
		} catch (\ErrorException $e) {
			throw new ExécutionException("Compilebox non disponible", 503, $e);
		}

		return json_decode(str_replace("\r", "", $comp_resp), true);
	}

	public function exécuter_sys($question, $tentative)
	{
		//post le code à remotecompiler
		$url_rc = $_ENV["COMPILEBOX_URL"];

		$tests_out = [];
		foreach ($question->tests as $test) {
			$tests_out[] = ["stdin" => $test->validation];
		}

		$data_rc = [
			"language" => self::langages["sshd"],
			"user" => $question->utilisateur,
			"parameters" => $tentative->conteneur,
			"params_conteneur" =>
				"-e SIAB_SERVICE=/:" . $question->utilisateur . ":" . $question->utilisateur . ":HOME:SHELL",
			"tests" => $tests_out,
			"vm_name" => $question->image,
		];

		$options_rc = [
			"http" => [
				"header" => "Content-type: application/x-www-form-urlencoded\r\n",
				"method" => "POST",
				"content" => http_build_query($data_rc),
			],
		];
		$context = stream_context_create($options_rc);

		try {
			$comp_resp = file_get_contents($url_rc, false, $context);
		} catch (\ErrorException $e) {
			throw new ExécutionException("Compilebox non disponible", 503, $e);
		}

		return json_decode(str_replace("\r", "", $comp_resp), true);
	}
}
