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

use progression\dao\DAOFactory;

class InteracteurFactory
{
	public function getLoginInt()
	{
		return new LoginInt(new DAOFactory());
	}
	public function getObtenirQuestionInt()
	{
		return new ObtenirQuestionInt(new DAOFactory());
	}
	public function getObtenirUserInt()
	{
		return new ObtenirUserInt(new DAOFactory());
	}
	public function getObtenirAvancementInt()
	{
		return new ObtenirAvancementInt(new DAOFactory());
	}
	public function getObtenirTentativeInt()
	{
		return new ObtenirTentativeInt(new DAOFactory());
	}
	public function getSoumettreTentativeProgInt()
	{
		return new SoumettreTentativeProgInt(new DAOFactory());
	}
	public function getPréparerProgInt()
	{
		return new PréparerProgInt();
	}
	public function getExécuterProgInt()
	{
		return new ExécuterProgInt(new DAOFactory());
	}
	public function getTraiterTentativeProgInt()
	{
		return new TraiterTentativeProgInt(new DAOFactory());
	}
	public function getSauvegarderAvancementProgIntTests()
	{
		return null;
	}
}
