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

namespace progression\domaine\interacteur;

use progression\dao\DAOException;
use progression\domaine\entité\Sauvegarde;

class EnregistrerSauvegardeInt extends Interacteur
{
	/**
	 * @return array<Sauvegarde>
	 */
	public function enregistrer($username, $question_uri, $langage, $sauvegarde): array
	{
		$dao_sauvegarde = $this->source_dao->get_sauvegarde_dao();
		try {
			$resultat_sauvegarde = $dao_sauvegarde->save($username, $question_uri, $langage, $sauvegarde);
		} catch (DAOException $e) {
			throw new IntéracteurException($e, 503);
		}
		return $resultat_sauvegarde;
	}
}
