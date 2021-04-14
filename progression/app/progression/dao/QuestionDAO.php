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
use Exception;
use progression\domaine\entité\{QuestionProg, QuestionSys, QuestionBD};
use ZipArchive;

class QuestionDAO extends EntitéDAO
{
	public function get_question($uri)
	{
		$infos_question = $this->récupérer_question($uri);

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
		$question->feedback_err = key_exists("feedback!", $infos_question) ? $infos_question["feedback!"] : null;
	}

	protected function récupérer_question($uri)
	{
		$entêtesInitiales = @get_headers($uri, 1);
		if (!$entêtesInitiales) {
			// Fichier test local
			try {
				$info = $this->récupérer_fichier_info($uri);
			} catch (Exception) {
				$archiveExtraite = self::extraire_zip($uri, substr($uri, 0, -4), true);
				$info = $this->récupérer_fichier_info("file://" . $archiveExtraite);
				self::supprimer_fichiers($archiveExtraite);
			}
		} else {
			$entêtesYml = self::vérifier_entêtes($uri . "/info.yml");
			if ($entêtesYml && $entêtesYml["Content-Type"] == "text/yaml; charset=utf-8") {
				$info = $this->récupérer_fichier_info($uri);
			} else {
				$info = $this->récupérer_archive($uri, $entêtesInitiales["Content-Type"]);
			}
		}

		return $info;
	}

	private function récupérer_fichier_info($uri)
	{
		$data = @file_get_contents($uri . "/info.yml");
		if ($data === false) {
			throw new RuntimeException("Le fichier ne peut pas être chargé");
		}

		$info = yaml_parse($data);
		if ($info === false) {
			throw new DomainException("Le fichier ne peut pas être décodé");
		}

		if (isset($info["type"]) && $info["type"] == "prog") {
			$info = $this->source->get_question_prog_dao()->récupérer_question($uri, $info);
		}

		$info["uri"] = $uri;
		return $info;
	}

	private function récupérer_archive($uri, $typeArchive)
	{
		if (!self::vérifier_entêtes($uri)) {
			return null;
		}

		switch ($typeArchive) {
			case "application/zip":
				$nomFichier = self::télécharger_fichier($uri);
				$archiveExtraite = self::extraire_zip($nomFichier, substr($nomFichier, 0, -4));
				break;
			default:
				return null;
		}

		$sortie = $this->récupérer_fichier_info("file://" . $archiveExtraite);
		self::supprimer_fichiers($archiveExtraite);

		return $sortie;
	}

	private static function vérifier_entêtes($uri)
	{
		$entêtes = @get_headers($uri, 1);
		if ($entêtes["Content-Length"] > $_ENV["QUESTION_TAILLE_MAX"]) {
			return false;
		}

		return $entêtes;
	}

	private static function télécharger_fichier($uri)
	{
		$nomUnique = uniqid("archive_", true);
		$chemin = sys_get_temp_dir() . "/$nomUnique.arc";

		if (file_put_contents($chemin, file_get_contents($uri))) {
			return $chemin;
		}

		return false;
	}

	private static function supprimer_fichiers($cheminCible)
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

	private static function extraire_zip($archive, $destination, $test = false)
	{
		$zip = new ZipArchive;
		if ($zip->open($archive) === true) {
			if (!$zip->extractTo($destination)) {
				return false;
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
