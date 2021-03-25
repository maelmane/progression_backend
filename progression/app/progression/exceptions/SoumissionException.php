<?php

class SoumissionException extends Exception
{
	public function errorMessage()
	{
		$erreur = "Erreur sur la ligne " . $this->getLine() . " dans " . $this->getFile();
		return $erreur;
	}
}
