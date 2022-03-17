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

namespace progression\domaine\entité;

class Commentaire
{
  public $id;
  public $message;
  public $créateur;	
  public $date;	
  public $numéro_ligne;

	public function __construct($id, $date, $message, $créateur,$numéro_ligne)
	{
		$this->id = $id;
		$this->date = $date;
		$this->message = $message;
		$this->créateur = $créateur;
    $this->numéro_ligne = $numéro_ligne;
	
	}
}
