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
use PHPUnit\Framework\TestCase;

final class QuestionTransformerTests extends TestCase
{

	public function test_étant_donné_une_questionprog_null_lorsquon_récupère_son_transformer_on_obtient_un_array_contenant_null()
	{
		$question = null;
		$item = (new QuestionTransformer())->transform($question);

		$this->assertEquals([null], $item);
	}

}
