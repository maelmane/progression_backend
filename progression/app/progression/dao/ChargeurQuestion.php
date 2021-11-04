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

use RuntimeException;

class ChargeurQuestion
{
	public function récupérer_question($uri)
	{
		$scheme = parse_url($uri, PHP_URL_SCHEME);

		if ($scheme == "file") {
			$sortie = (new ChargeurQuestionFichier())->récupérer_question($uri);
		} elseif ($scheme == "https") {
			$sortie = $this->récupérer_question_http($uri);
		} else {
			throw new RuntimeException("Schéma d'URI invalide");
		}

		$sortie["uri"] = $uri;

		return $sortie;
	}

	private function récupérer_question_http($uri)
	{
		$content_type = $this->get_entête($uri, "Content-Type");

		if ($content_type) {
			if (str_starts_with($content_type, "application/zip")) {
				return (new ChargeurQuestionArchive())->récupérer_question($uri);
			}

			if (str_starts_with($content_type, "text/yaml")) {
				return (new ChargeurQuestionFichier())->récupérer_question($uri);
			}

			throw new RuntimeException("Type d'archive {$content_type} non implémenté");
		}

		throw new RuntimeException("Type de fichier inconnu");
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
}
