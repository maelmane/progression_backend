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
use Illuminate\Support\Facades\Log;
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
			throw new DomainException("Le fichier ne peut pas être décodé (type inconnu)");
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
		$content_type = $this->get_entête($uri, "Content-Type");

		if (!$content_type) {
			$info = $this->récupérer_fichier_info($uri);
		} else {
			if (str_starts_with($content_type, "application")) {
				return $this->récupérer_archive($uri);
			}

			if ($this->get_entête($uri . "/info.yml", "Content-Type") == "text/yaml") {
				return $this->récupérer_fichier_info($uri);
			}

			throw new RuntimeException("Type d'archive {$content_type} non implémenté");
		}

		return $info;
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

	private function récupérer_fichier_info($uri)
	{
		$data = @file_get_contents($uri . (str_ends_with($uri, "/info.yml") ? "" : "/info.yml"));
		if ($data === false) {
			throw new RuntimeException("Le fichier {$uri} ne peut pas être chargé");
		}

		$info = yaml_parse($data);
		if ($info === false) {
			throw new DomainException("Le fichier ne peut pas être décodé (format invalide)");
		}

		if (isset($info["type"]) && $info["type"] == "prog") {
			$info = $this->source->get_question_prog_dao()->récupérer_question($uri, $info);
		}

		$info["uri"] = $uri;
		return $info;
	}

	private function récupérer_archive($uri)
	{
		self::vérifier_entêtes($uri);

		$nomFichier = self::télécharger_fichier($uri);
		$archiveExtraite = self::extraire_zip($nomFichier, substr($nomFichier, 0, -4));

		$sortie = $this->récupérer_fichier_info("file://" . $archiveExtraite);
		self::supprimer_fichiers($archiveExtraite);

		return $sortie;
	}

	private function vérifier_entêtes($uri)
	{
		$taille = $this->get_entête($uri, "Content-Length");

		if (!$taille) {
			throw new LengthException("Le fichier de taille inconnue. On ne le chargera pas.");
		}

		if ($taille > $_ENV["QUESTION_TAILLE_MAX"]) {
			throw new LengthException("Le fichier est trop volumineux pour être chargé: " . $taille);
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
