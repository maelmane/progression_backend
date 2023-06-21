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

class ConfigTransformer extends BaseTransformer
{
	public string $type = "config";

	/**
	 * @param array<mixed> $config
	 * @return array<mixed>
	 */
	public function transform(array $config): array
	{
		$data_out = $config + [
			"id" => $config["id"],
			"links" => [
				"self" => "{$this->urlBase}/",
				"inscrire" => "{$this->urlBase}/user/",
			],
		];

		return $data_out;
	}
}
