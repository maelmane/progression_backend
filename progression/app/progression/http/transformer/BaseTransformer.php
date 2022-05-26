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

use League\Fractal;

class BaseTransformer extends Fractal\TransformerAbstract
{
	public $id;

	public function __construct($id){
		$this->id = $id;
	}

	protected function sélectionnerChamps($objet, $fields)
	{
		$arr_t = (array) $objet;
		foreach ($arr_t as $field => $value) {
			if (!in_array($field, $fields)) {
				$arr_t[$field] = null;
			}
		}

		$objet = (object) $arr_t;

		return $objet;
	}

	protected function validerParams($params)
	{
		if ($params) {
			// Optional params validation
			$usedParams = array_keys(iterator_to_array($params));
			if ($invalidParams = array_diff($usedParams, $this->availableParams)) {
				throw new \Exception(sprintf('Paramètres invalides : "%s"', implode(",", $usedParams)));
			}

			return iterator_to_array($params);
		} else {
			return null;
		}
	}
}
