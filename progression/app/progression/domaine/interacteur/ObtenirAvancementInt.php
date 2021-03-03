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

class ObtenirAvancementInt extends Interacteur
{
	function __construct($source)
	{
		$this->_source = $source;
	}

	function get_avancement($user_id, $question_id)
	{
		return $this->_source
			->get_avancement_dao()
			->get_avancement($user_id, $question_id);
	}

	function get_tentative($user_id, $question_id, $date)
	{
		return $this->_source
			->get_avancement_dao()
			->get_tentative($user_id, $question_id, $date);
	}
}
