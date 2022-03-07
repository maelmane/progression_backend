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

use progression\TestCase;

use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\{User, Clé};
use progression\dao\DAOFactory;
use Illuminate\Http\Request;
use Illuminate\Auth\GenericUser;
use Firebase\JWT\JWT;
use progression\http\contrôleur\JetonCtl;

final class JetonCtlTests extends TestCase
{
	public function test_création_de_jeton_pour_lien_vers_exercice_par_autre_fonctionn() {
		$jetonCtrl = new JetonCtl();

		

	}



}