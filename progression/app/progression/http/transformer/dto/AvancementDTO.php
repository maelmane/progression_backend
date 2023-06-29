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

use progression\http\contrôleur\{TentativeCtl, SauvegardeCtl};
use progression\domaine\entité\{Avancement, Tentative, Sauvegarde};

class AvancementDTO extends GénériqueDTO
{
	/**
	 * @var array<Tentative>|null $tentatives
	 */
	public array|null $tentatives;
	/**
	 * @var array<Sauvegarde>|null $sauvegardes
	 */
	public array|null $sauvegardes;

	/**
	 * @param array<string> $liens
	 */
	public function __construct(string $id, mixed $objet, array $liens)
	{
		parent::__construct($id, $objet, $liens);

		$this->tentatives = [];
		foreach ($objet->tentatives as $date_soumission => $tentative) {
			array_push(
				$this->tentatives,
				new TentativeDTO(
					id: "{$id}/{$tentative->date_soumission}",
					objet: $tentative,
					liens: TentativeCtl::get_liens($id, $date_soumission),
				),
			);
		}
		$this->sauvegardes = [];
		foreach ($objet->sauvegardes as $langage => $sauvegarde) {
			array_push(
				$this->sauvegardes,
				new GénériqueDTO(
					id: "{$id}/{$langage}",
					objet: $sauvegarde,
					liens: SauvegardeCtl::get_liens($id, $langage),
				),
			);
		}
	}
}
