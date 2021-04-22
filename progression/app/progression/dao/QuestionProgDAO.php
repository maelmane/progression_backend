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

use progression\domaine\entité\{Exécutable, Test};
use RuntimeException, DomainException;

class QuestionProgDAO extends EntitéDAO
{
	public function load($question, $infos_question)
	{
		$question->exécutables = $this->load_exécutables($question, $infos_question);
		$question->tests = $this->load_tests($question, $infos_question);
	}

	protected function load_exécutables($question, $infos_question)
	{
		$exécutables = [];
		foreach ($infos_question["execs"] as $lang => $code) {
			$exécutables[$lang] = new Exécutable($code, $lang);
		}

		return $exécutables;
	}

	protected function load_tests($question, $infos_question)
	{
		$tests = [];
		foreach ($infos_question["tests"] as $test) {
			$tests[] = new Test(
				$test["nom"],
				$test["in"],
				$test["out"],
				key_exists("params", $test) ? $test["params"] : null,
				key_exists("feedback+", $test) ? $test["feedback+"] : null,
				key_exists("feedback-", $test) ? $test["feedback-"] : null,
				key_exists("feedback!", $test) ? $test["feedback!"] : null,
			);
		}

		return $tests;
	}

	public function récupérer_question($uri, $info)
	{
		$exécutables = $this->récupérer_execs($uri, $info["execs"]);

		if ($exécutables === null) {
			return null;
		} else {
			$info["execs"] = $exécutables;
		}

		$tests = $this->récupérer_tests($uri, $info["tests"]);
		if ($tests === null) {
			return null;
		} else {
			$info["tests"] = $tests;
		}

		return $info;
	}

	protected function récupérer_execs($uri, $execs)
	{
		$items = [];

		foreach ($execs as $exec) {
			$exécutable = $this->récupérer_exec($uri, $exec["fichier"]);

			if ($exécutable === null) {
				return null;
			}

			$items[$exec["langage"]] = $exécutable;
		}

		return $items;
	}

	protected function récupérer_exec($uri, $exec)
	{
		$data = @file_get_contents($uri . "/" . $exec);

		if ($data === false) {
			error_log("$uri/$exec ne peut pas être chargé");
			throw new RuntimeException("Le fichier ne peut pas être chargé");
			return null;
		} else {
			return $data;
		}
	}

	protected function récupérer_tests($uri, $tests)
	{
		$items = [];

		foreach ($tests as $test) {
			$data = @file_get_contents($uri . "/" . $test);

			if ($data === false) {
				error_log("$uri/$test ne peut pas être chargé");
				throw new RuntimeException("Le fichier ne peut pas être chargé");
				return null;
			}

			$items = array_merge($items, yaml_parse($data, -1));
			if ($items === false) {
				error_log("$uri/$test ne peut pas être décodé");
				throw new DomainException("Le fichier ne peut pas être décodé");
				return null;
			}
		}

		return $items;
	}
}
