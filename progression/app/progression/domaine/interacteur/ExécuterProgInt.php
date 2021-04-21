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

use Exception;
use progression\domaine\entité\RésultatProg;

class ExécuterProgInt extends Interacteur
{
	public function exécuter($exécutable, $test)
	{
		$this->loguer_code($exécutable);

		$comp_resp=$this->source_dao->get_exécuteur()->exécuter($exécutable, $test);

		if ($comp_resp === false) {
			$erreur = error_get_last();
			throw new ExécutionException($erreur, $_ENV["COMPILEBOX_URL"]);
		}

		return new RésultatProg(
			$this->extraire_sortie_standard($comp_resp),
			$this->extraire_sortie_erreur($comp_resp)
		);
	}

	protected function loguer_code($exécutable)
	{
		$com_log =
			$_SERVER["REMOTE_ADDR"] .
			" - " .
			$_SERVER["PHP_SELF"] .
			" : lang : " .
			$exécutable->lang .
			" Code : " .
			$exécutable->code;
		syslog(LOG_INFO, $com_log);
	}

	protected function extraire_sortie_standard($sorties)
	{
		return str_replace("\r", "", json_decode($sorties, true)["output"]);
	}

	protected function extraire_sortie_erreur($sorties)
	{
		return json_decode($sorties, true)["errors"];
	}
}

class ExécutionException extends Exception
{
	public function __construct($erreur, $url)
	{
		$erreurMsg = (isset($erreur) && isset($erreur["message"]) && $erreur["message"] != "") ?
					 $erreur["message"] : "Échec de l'ouverture du fichier a l'adresse : {$url}";
		parent::__construct($erreurMsg);
	}
}
