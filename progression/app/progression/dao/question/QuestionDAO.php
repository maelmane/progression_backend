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

namespace progression\dao\question;

use DomainException;
use RuntimeException;
use Illuminate\Support\Facades\Log;
use progression\dao\EntitéDAO;
use progression\domaine\entité\{QuestionProg, QuestionSys, QuestionBD};

class QuestionDAO extends EntitéDAO
{
	public function get_question($uri, $chargeur = null)
	{
		$infos_question = ($chargeur ?? new ChargeurQuestion())->récupérer_question($uri);

		if ($infos_question === null) {
			throw new DomainException("Le fichier ne peut pas être décodé. Erreur inconnue.");
		}

		$type = $infos_question["type"] ?? ($type = "prog");

		if ($type == "prog") {
			return DécodeurQuestionProg::load($infos_question);
		} elseif ($type == "sys") {
			throw new RuntimeException("Question de type SYS non implémenté");
		} elseif ($type == "bd") {
			throw new RuntimeException("Question de type SYS non implémenté");
		} else {
			throw new DomainException("Le fichier ne peut pas être décodé. Type inconnu");
		}
	}
}
