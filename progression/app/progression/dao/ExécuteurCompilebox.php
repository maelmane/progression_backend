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

namespace progression\dao;

class ExécuteurCompilebox extends Exécuteur{
	const langages = [
		"python2" => 0,
		"python" => 1,
		"ruby" => 2,
		"clojure" => 3,
		"php" => 4,
		"nodejs" => 5,
		"scala" => 6,
		"go" => 7,
		"cpp" => 8,
		"c" => 9,
		"java" => 10,
		"bash" => 11,
		"perl" => 12,
		"sshd" => 13,
		"mysql" => 14,
	];

	public function exécuter($exécutable, $test){
		//post le code à remotecompiler
		$url_rc = $_ENV["COMPILEBOX_URL"];

		$data_rc = [
			"language" => self::langages[$exécutable->lang],
			"code" => $exécutable->code,
			"parameters" => "\"" . $test->params . "\"",
			"stdin" => $test->entrée,
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

		$comp_resp = @file_get_contents($url_rc, false, $context);

		return $comp_resp;
	}

}
