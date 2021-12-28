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

class ChargeurQuestionHTTP
{
	public static function récupérer_question($uri)
	{
		$entêtes = self::get_entêtes($uri);
		$content_type = self::get_entête($entêtes, "content-type");

		if ($content_type) {
			if (str_starts_with($content_type, "application")) {
				return (new ChargeurQuestionArchive())->récupérer_question($uri, $entêtes);
			}

			if (str_starts_with($content_type, "text")) {
				return (new ChargeurQuestionFichier())->récupérer_question($uri);
			}

			throw new RuntimeException("Type d'archive {$content_type} non implémenté");
		}

		throw new RuntimeException("Type de fichier inconnu");
	}

	private static function get_entêtes($uri)
	{
		$opts = [
			"http" => [
				"follow_location" => 1,
			],
		];
		$context = stream_context_create($opts);
		return array_change_key_case(@get_headers($uri, 1, $context));
	}

	private static function get_entête($entêtes, $clé)
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
}
