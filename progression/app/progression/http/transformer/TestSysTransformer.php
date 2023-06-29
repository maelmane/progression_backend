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

namespace progression\http\transformer;

use progression\domaine\entité\TestSys;
use progression\http\transformer\dto\GénériqueDTO;
use League\Fractal;

class TestSysTransformer extends BaseTransformer
{
	public $type = "test";

	public function transform(GénériqueDTO $data_in)
	{
		$test = $data_in->objet;

		$data_out = (new TestTransformer())->transform($data_in);
		$data_out = array_merge($data_out, [
			"validation" => $test->validation,
			"utilisateur" => $test->utilisateur,
		]);

		return $data_out;
	}
}
