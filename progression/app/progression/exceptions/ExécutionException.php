<?php

namespace progression\exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ExécutionException extends Exception
{
	public function __construct($url)
	{
		$errorMsg =
			"Erreur à la ligne " . $this->getLine() .
			" dans le ficher " . $this->getFile() .
			" : Échec de l'ouverture du fichier a l'adresse " . $url;
		Log::error($errorMsg);
		parent::__construct("Service non disponible");
	}
}
