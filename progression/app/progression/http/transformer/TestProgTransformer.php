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

use progression\domaine\entité\{Test, TestProg};
use League\Fractal;

class TestProgTransformer extends BaseTransformer
{
	public $type = "test";

	public function transform(TestProg $test)
	{
		$data = (new TestTransformer($this->id))->transform($test);
		$data = array_merge($data, [
			"entrée" => $test->caché ? null : $test->entrée,
			"params" => $test->caché ? null : $test->params,
		]);

		return $data;
	}
}
