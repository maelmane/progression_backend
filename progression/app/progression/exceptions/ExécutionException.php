<?php

namespace progression\exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ExécutionException extends Exception
{
	public function __construct($erreur, $url)
	{
		$erreurMsg = (isset($erreur) && isset($erreur["message"]) && $erreur["message"] != "") ?
			$erreur["message"] : "Échec de l'ouverture du fichier a l'adresse : {$url}";

		Log::error($erreurMsg);
		parent::__construct("Service non disponible");
	}
}
