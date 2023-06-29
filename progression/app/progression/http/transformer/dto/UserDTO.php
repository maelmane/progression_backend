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
use progression\http\contrôleur\{AvancementCtl, CléCtl};
use progression\domaine\entité\Avancement;
use progression\domaine\entité\clé\Clé;

class UserDTO extends GénériqueDTO
{
	/**
	 * @var array<Avancement> $avancements
	 */
	public array $avancements;
	/**
	 * @var array<Clé> $clés
	 */
	public array $clés;

	/**
	 * @param array<string> $liens
	 */
	public function __construct(mixed $id, mixed $objet, array $liens)
	{
		parent::__construct($id, $objet, $liens);

		$this->avancements = [];
		foreach ($objet->avancements as $uri => $avancement) {
			array_push(
				$this->avancements,
				new AvancementDTO(id: "{$id}/{$uri}", objet: $avancement, liens: AvancementCtl::get_liens($id, $uri)),
			);
		}
		$this->clés = [];
		foreach ($objet->clés as $uri => $clé) {
			array_push(
				$this->clés,
				new GénériqueDTO(id: "{$id}/{$uri}", objet: $clé, liens: CléCtl::get_liens($id, $uri)),
			);
		}
	}
}
