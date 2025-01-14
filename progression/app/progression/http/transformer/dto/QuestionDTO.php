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

namespace progression\http\transformer\dto;

use progression\http\contrôleur\TestCtl;
use progression\domaine\entité\Test;

class QuestionDTO extends GénériqueDTO
{
	/**
	 * @var array<Test> $tests
	 */
	public array $tests;

	/**
	 * @param array<string> $liens
	 */
	public function __construct(mixed $id, mixed $objet, array $liens)
	{
		parent::__construct($id, $objet, $liens);

		$this->tests = [];
		foreach ($objet->tests as $numéro => $test) {
			array_push(
				$this->tests,
				new GénériqueDTO(id: "{$id}/{$numéro}", objet: $test, liens: TestCtl::get_liens($id, $numéro)),
			);
		}
	}
}
