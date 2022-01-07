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

use RuntimeException;

class ChargeurQuestionHTTP extends Chargeur
{
	public function récupérer_question($uri)
	{
		$entêtes = $this->source->get_chargeur_http()->get_entêtes($uri);

		$taille = self::get_entête($entêtes, "content-length");
		$content_type = self::get_entête($entêtes, "content-type");
		$nom_archive = self::get_entête($entêtes, "content-disposition");

		self::vérifier_taille($taille);
		self::vérifier_type($content_type);

		$question = null;
		if (str_starts_with($content_type, "application")) {
			self::vérifier_nom_archive($nom_archive);
			$question = self::extraire_archive($uri);
		} elseif (str_starts_with($content_type, "text")) {
			$question = $this->source->get_chargeur_question_fichier()->récupérer_question($uri);
		}

		return $question;
	}

	private function get_entête($entêtes, $clé)
	{
		if ($entêtes == null) {
			return null;
		}
		$content_type = $entêtes[$clé];

		if (is_string($content_type)) {
			return $content_type;
		}

		if (is_array($content_type)) {
			return $content_type[count($content_type) - 1];
		}
	}

	private function vérifier_taille($taille)
	{
		if (!$taille) {
			throw new ChargeurException("Le fichier de taille inconnue. On ne le chargera pas.");
		}

		if ($taille > $_ENV["QUESTION_TAILLE_MAX"]) {
			throw new ChargeurException("Le fichier est trop volumineux pour être chargé: " . $taille);
		}
	}

	private function vérifier_type($type)
	{
		if (!in_array($type, ["application", "text"])) {
			throw new ChargeurException("Impossible de charger le fichier de type $type");
		}
	}

	private function extraire_archive($uri)
	{
		$chemin_fichier = self::télécharger_fichier($uri);
		try {
			$question = $this->source->get_chargeur_question_archive()->récupérer_question($chemin_fichier);
		} catch (ChargeurException $e) {
			throw $e;
		} finally {
			unlink($chemin_fichier);
		}
	}

	private function vérifier_nom_archive($nom_archive)
	{
		preg_match('/filename=\"(.*\.zip)\"/', $nom_archive, $résultats);
		if (!array_key_exists(1, $résultats)) {
			throw new ChargeurException("Impossible de charger l'archive $nom_archive");
		}
		return $résultats[1];
	}

	private function télécharger_fichier($uri)
	{
		$nomUnique = uniqid("archive_", true);
		$chemin = sys_get_temp_dir() . "/$nomUnique.arc";

		$contenu = $this->source->get_chargeur_http()->get_url($uri);

		if ($contenu === false) {
			throw new ChargeurException("Impossible de charger le fichier archive $uri");
		}

		if (file_put_contents($chemin, $contenu)) {
			return $chemin;
		}

		return false;
	}
}
