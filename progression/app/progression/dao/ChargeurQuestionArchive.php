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

namespace progression\dao;

use LengthException, RuntimeException;
use Exception;
use ZipArchive;

class ChargeurQuestionArchive
{
	public function récupérer_question($uri)
	{
		self::vérifier_taille($uri);

		$nomFichier = null;
		$archiveExtraite = null;

		try {
			$nomFichier = self::télécharger_fichier($uri);
			$archiveExtraite = self::extraire_zip($nomFichier, substr($nomFichier, 0, -4));

			$sortie = (new ChargeurQuestionFichier())->récupérer_question("file://" . $archiveExtraite . "/info.yml");
		} catch (Exception $e) {
			throw $e;
		} finally {
			if ($nomFichier) {
				self::supprimer_fichiers($nomFichier);
			}
			if ($archiveExtraite) {
				self::supprimer_fichiers($archiveExtraite);
			}
		}

		return $sortie;
	}

	private function vérifier_taille($uri)
	{
		$taille = $this->get_entête($uri, "Content-Length");

		if (!$taille) {
			throw new LengthException("Le fichier de taille inconnue. On ne le chargera pas.");
		}

		if ($taille > $_ENV["QUESTION_TAILLE_MAX"]) {
			throw new LengthException("Le fichier est trop volumineux pour être chargé: " . $taille);
		}
	}

	private function get_entête($uri, $clé)
	{
		$opts = [
			"http" => [
				"follow_location" => 1,
			],
		];
		$context = stream_context_create($opts);
		$entêtes = @get_headers($uri, 1, $context);

		if ($entêtes != null) {
			$content_type = $entêtes[$clé];

			if (is_string($content_type)) {
				return $content_type;
			}

			if (is_array($content_type)) {
				return $content_type[count($content_type) - 1];
			}
		} else {
			return null;
		}
	}

	private function télécharger_fichier($uri)
	{
		$nomUnique = uniqid("archive_", true);
		$chemin = sys_get_temp_dir() . "/$nomUnique.arc";

		$contenu = file_get_contents($uri);

		if ($contenu === false) {
			throw new RuntimeException("Impossible de charger le fichier archive $uri");
		}

		if (file_put_contents($chemin, $contenu)) {
			return $chemin;
		}

		return false;
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

	private function extraire_zip($archive, $destination, $test = false)
	{
		$zip = new ZipArchive();
		if ($zip->open($archive) === true) {
			if (!$zip->extractTo($destination)) {
				throw new RuntimeException("Impossible de décompresser l'archive");
			} else {
				if (!$test) {
					self::supprimer_fichiers($archive);
				}
				return $destination;
			}
			$zip->close();
		}

		return false;
	}
}
