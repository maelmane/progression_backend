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

use progression\domaine\entité\user\{User, État, Rôle, Occupation};
use progression\util\Encodage;
use League\Fractal\Resource\Collection;
use progression\http\contrôleur\AvancementCtl;
use progression\http\transformer\dto\UserDTO;

class UserTransformer extends BaseTransformer
{
	public $type = "user";

	protected array $availableIncludes = ["avancements", "cles"];

	public function transform(UserDTO $data_in)
	{
		$id = $data_in->id;
		$user = $data_in->objet;
		$liens = $data_in->liens;

		$data = [
			"id" => $id,
			"courriel" => $user->courriel,
			"username" => $user->username,
			"date_inscription" => $user->date_inscription,
			"état" => match ($user->état) {
				État::INACTIF => "inactif",
				État::ACTIF => "actif",
				État::EN_ATTENTE_DE_VALIDATION => "en_attente_de_validation",
				default => "indéfini",
			},
			"rôle" => match ($user->rôle) {
				Rôle::NORMAL => "normal",
				Rôle::ADMIN => "admin",
				default => "indéfini",
			},
			"préférences" => $user->préférences,
			"links" => $liens,
			"nom" => $user->nom,
			"prenom" => $user->prénom,
			"nom_complet" => $user->nom_complet,
			"pseudo" => $user->pseudo,
			"biographie" => $user->biographie,
			"occupation" => match ($user->occupation) {
				Occupation::ETUDIANT => "étudiant",
				Occupation::ENSEIGNANT => "enseignant",
				Occupation::TUTEUR => "tuteur",
				default => "utilisateur autre",
			},
			"avatar" => $user->avatar,
		];

		return $data;
	}

	public function includeAvancements(UserDTO $data_in): Collection
	{
		$user = $data_in->objet;

		return $this->collection($data_in->avancements, new AvancementTransformer(), "avancement");
	}

	public function includeCles(UserDTO $data_in): Collection
	{
		$user = $data_in->objet;

		return $this->collection($data_in->clés, new CléTransformer(), "cle");
	}
}
