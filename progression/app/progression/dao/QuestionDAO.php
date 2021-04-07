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

use DomainException, LengthException, RuntimeException;
use progression\domaine\entité\{QuestionProg, QuestionSys, QuestionBD};
use ZipArchive;

class QuestionDAO extends EntitéDAO
{
	public function get_question($uri, $format = "auto")
	{
		$infos_question = $this->récupérer_question($uri, $format);

		if ($infos_question === null) {
			throw new DomainException("Le fichier ne peut pas être décodé");
		}

		$question = null;
		if (key_exists("type", $infos_question)) {
			$type = $infos_question["type"];

			if ($type == "prog") {
				$question = new QuestionProg();
				$this->load($question, $infos_question);
				$this->source->get_question_prog_dao()->load($question, $infos_question);
			} elseif ($type == "sys") {
				$question = new QuestionSys();
				$this->load($question, $infos_question);
				$this->source->get_question_sys_dao()->load($question, $infos_question);
			} elseif ($type == "bd") {
				$question = new QuestionBD();
				$this->source->get_question_bd_dao()->load($question, $infos_question);
			}
		} else {
			throw new DomainException("Le fichier ne peut pas être décodé");
		}
		return $question;
	}

	protected function load($question, $infos_question)
	{
		$question->uri = $infos_question["uri"];
		$question->titre = $infos_question["titre"];
		$question->description = $infos_question["description"];
		$question->enonce = $infos_question["énoncé"];
		$question->feedback_pos = key_exists("feedback+", $infos_question) ? $infos_question["feedback+"] : null;
		$question->feedback_neg = key_exists("feedback-", $infos_question) ? $infos_question["feedback-"] : null;
	}

	protected function récupérer_question($uri, $format)
	{
		$entêtesInitiales = @get_headers($uri, 1);

		if (!$entêtesInitiales) {
			// Fichier test local
			$info = $this->récupérer_fichier_info($uri);
		} elseif ($format == "auto") {

			switch ($entêtesInitiales["Content-Type"]) {
				case "application/zip":
					self::vérifierEntêtes($uri);
					$nomFichier = self::téléchargerFichier($uri);
					$archiveExtraite = self::extraireZip($nomFichier, substr($nomFichier, 0, -4));
					$info = $this->récupérer_fichier_info("file://" . $archiveExtraite);
					break;

				case "text/plain":
					self::vérifierEntêtes($uri . "/info.yml", true);
					$info = $this->récupérer_fichier_info($uri);
					break;

				default:
					$info["uri"] = $uri;
			}
		} else {
			$info["uri"] = $uri;
		}

		return $info;
	}

	private function récupérer_fichier_info($uri)
	{
		$data = @file_get_contents($uri . "/info.yml");
		if ($data === false) {
			error_log("$uri ne peut pas être chargé");
			throw new RuntimeException("Le fichier ne peut pas être chargé");
		}

		$info = yaml_parse($data);
		if ($info === false) {
			error_log("$uri ne peut pas être décodé");
			throw new DomainException("Le fichier ne peut pas être décodé");
		}

		if (isset($info["type"]) && $info["type"] == "prog") {
			$info = $this->source->get_question_prog_dao()->récupérer_question($uri, $info);
		}

		$info["uri"] = $uri;
		return $info;
	}

	private static function vérifierEntêtes($uri, $estUnYml = false)
	{
		if ($estUnYml) {
			$entêtes = @get_headers($uri . "/info.yml", 1);
			if ($entêtes["Content-Length"] > $_ENV["LIMITE_YML"]) {
				throw new LengthException("Le fichier est trop volumineux pour être chargé");
			}
		} else {
			$entêtes = @get_headers($uri, 1);
			if ($entêtes["Content-Length"] > $_ENV["LIMITE_ARCHIVE"]) {
				throw new LengthException("Le fichier est trop volumineux pour être chargé");
			}
		}

		return $entêtes;
	}

	private static function téléchargerFichier($uri)
	{
		$nomUnique = uniqid("archive_", true);
		$chemin = sys_get_temp_dir() . "/$nomUnique.zip";

		if (file_put_contents($chemin, file_get_contents($uri))) {
			return $chemin;
		} else {
			throw new RuntimeException("Le fichier ne peut pas être téléchargé");
		}
	}

	private static function supprimerFichier($cheminFichier)
	{
		$fichierÀSupprimer = unlink($cheminFichier);
		if (!$fichierÀSupprimer) {
			throw new RuntimeException("Le fichier ne peut pas être supprimé");
		} else {
			return true;
		}
	}

	private static function extraireZip($archive, $destination)
	{
		$zip = new ZipArchive;

		if ($zip->open($archive) === true) {
			if (!$zip->extractTo($destination)) {
				throw new RuntimeException("Le fichier ne peut pas être sauvegardé");
			} else {
				self::supprimerFichier($archive);
				return $destination;
			}
			$zip->close();
		} else {
			throw new DomainException("Le fichier ne peut pas être décodé");
		}
	}
}
