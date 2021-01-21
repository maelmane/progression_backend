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
?><?php

require_once __DIR__ . '/interacteur.php';

class ExécuterProgInt extends Interacteur
{
	function __construct($source, $user_id)
	{
		parent::__construct($source);
		$this->_user_id = $user_id;
	}

	function exécuter($exécutable, $test)
	{
		ExécuterProgInt::loguer_code($exécutable);

		//post le code à remotecompiler
		$url_rc =
			'http://' .
			$GLOBALS['config']['compilebox_hote'] .
			':' .
			$GLOBALS['config']['compilebox_port'] .
			'/compile'; //TODO à changer ?
		$data_rc = [
			'language' => $exécutable->lang,
			'code' => $exécutable->code_exec,
			'parameters' => "\"" . $test->params . "\"",
			'stdin' => $test->stdin,
			'vm_name' => 'remotecompiler',
		];

		$options_rc = [
			'http' => [
				'header' =>
					"Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'POST',
				'content' => http_build_query($data_rc),
			],
		];
		$context = stream_context_create($options_rc);

		$comp_resp = file_get_contents($url_rc, false, $context);

		return [
			"stdout" => ExécuterProgInt::extraire_sortie_standard($comp_resp),
			"stderr" => ExécuterProgInt::extraire_sortie_erreur($comp_resp),
		];
	}

	protected function loguer_code($exécutable)
	{
		$com_log =
			$_SERVER['REMOTE_ADDR'] .
			" - " .
			$_SERVER["PHP_SELF"] .
			" : lang : " .
			$exécutable->lang .
			" Code : " .
			$exécutable->code_utilisateur;
		syslog(LOG_INFO, $com_log);
	}

	protected function extraire_sortie_standard($sorties)
	{
		return str_replace("\r", "", json_decode($sorties, true)['output']);
	}

	protected function extraire_sortie_erreur($sorties)
	{
		return json_decode($sorties, true)['errors'];
	}
}
