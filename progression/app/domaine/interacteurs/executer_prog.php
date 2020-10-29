<?php

require_once __DIR__ . '/interacteur.php';

class ExécuterProgInt extends Interacteur
{
	function __construct($source, $user_id)
	{
		parent::__construct($source);
		$this->_user_id = $user_id;
	}

	function exécuter($exécutable)
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
			'language' => $exécutable->langid,
			'code' => $exécutable->code_exec,
			'parameters' => "\"$exécutable->params\"",
			'stdin' => $exécutable->stdin,
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
			$exécutable->langid .
			" Code : " .
			$exécutable->code; //TODO à changer ?
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
