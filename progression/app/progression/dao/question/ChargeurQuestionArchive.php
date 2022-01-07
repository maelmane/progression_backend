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

use ZipArchive;

class ChargeurQuestionArchive extends Chargeur
{
	public function récupérer_question($chemin_fichier)
	{
		$archiveExtraite = null;

		$archiveExtraite = self::extraire_zip($chemin_fichier, substr($chemin_fichier, 0, -4));
		try {
			$question = $this->source
				->get_chargeur_question_fichier()
				->récupérer_question("file://" . $archiveExtraite . "/info.yml");
		} catch (ChargeurException $e) {
			throw $e;
		} finally {
			self::supprimer_fichiers($archiveExtraite);
		}

		return $question;
	}

	private function extraire_zip($archive, $destination)
	{
		$zip = new ZipArchive();
		$res = $zip->open($archive);
		if ($res !== true) {
			throw new ChargeurException("Impossible de lire l'archive $archive (err.: $res)");
		}
		$res = $zip->extractTo($destination);
		if ($res !== true) {
			throw new ChargeurException("Impossible de décompresser l'archive $archive (err.: $res)");
		}

		$zip->close();

		return $destination;
	}

	private function supprimer_fichiers($cheminCible)
	{
		if (PHP_OS === "Windows") {
			exec(sprintf("rd /s /q %s", escapeshellarg($cheminCible)));
			return true;
		} else {
			exec(sprintf("rm -rf %s", escapeshellarg($cheminCible)));
			return true;
		}
		return false;
	}
}
