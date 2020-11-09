<?php

class PréparerProgInt
{
	public function préparer_exécutable($question, $avancement, $langage, $code)
	{
		$exécutable = $question->exécutables[$langage];

		$exécutable->code_utilisateur = PréparerProgInt::sélectionner_code_utilisateur(
			$exécutable,
			$avancement,
			$code
		);
		$exécutable->code_exec = $exécutable->code_utilisateur;

		return $exécutable;
	}

	private function sélectionner_code_utilisateur(
		$exécutable,
		$avancement,
		$code
	) {
		if ($code != null) {
			return PréparerProgInt::composer_code_à_exécuter(
				$exécutable->code_utilisateur,
				$code
			);
		} elseif ($code == null && $avancement->code_utilisateur != null) {
			return $avancement->code_utilisateur;
		} else {
			return $exécutable->code_utilisateur;
		}
	}

	private function composer_code_à_exécuter($code_utilisateur, $code_todos)
	{
		//$code = preg_replace("/.*[+-]VISIBLE.*\n/", "", $code);
		$i = 0;
		$compte = 1;
		while ($compte > 0 && $i < count($code_todos)) {
			$code_utilisateur = preg_replace(
				"/(.*?\+TODO)(.|\n)*?(.*?-TODO)/",
				"$1\n$code_todos[$i]\n$3",
				$code_utilisateur,
				1,
				$compte
			);
			$i++;
		}
		return $code_utilisateur;
	}
}
?>
