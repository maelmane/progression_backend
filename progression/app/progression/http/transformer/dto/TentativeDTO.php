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

use progression\http\contrôleur\{RésultatCtl, CommentaireCtl};
use progression\domaine\entité\{Résultat, Commentaire};

class TentativeDTO extends GénériqueDTO
{
	/**
	 * @var array<Résultat> $résultats
	 */
	public array $résultats;
	/**
	 * @var array<Commentaire> $commentaires
	 */
	public array $commentaires;

	/**
	 * @param array<string> $liens
	 */
	public function __construct(mixed $id, mixed $objet, array $liens)
	{
		parent::__construct($id, $objet, $liens);

		$this->résultats = [];
		foreach ($objet->résultats as $hash => $résultat) {
			array_push(
				$this->résultats,
				new GénériqueDTO(
					id: $hash,
					objet: $résultat,
					liens: RésultatCtl::get_liens($hash) + [
						"tentative" => "{$this->urlBase}/tentative/{$id}",
					],
				),
			);
		}

		$this->commentaires = [];
		foreach ($objet->commentaires as $id_commentaire => $commentaire) {
			array_push(
				$this->commentaires,
				new GénériqueDTO(
					id: "{$id}/{$id_commentaire}",
					objet: $commentaire,
					liens: CommentaireCtl::get_liens($id, $id_commentaire, $commentaire),
				),
			);
		}
	}
}
