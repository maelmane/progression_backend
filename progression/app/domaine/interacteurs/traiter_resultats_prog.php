<?php

class TraiterRésultatsProgInt extends Interacteur
{
	function __construct($source, $user_id)
	{
		parent::__construct($source);
		$this->_user_id = $user_id;
	}

	function traiter_résultats($exécutable, $tests, $question)
	{
		$résultats = [];

		$avancement = (new ObtenirAvancementInt(
			$this->_source,
			$this->_user_id
		))->get_avancement($question->id, $this->_user_id);
		$avancement->code = $exécutable->code_utilisateur;

		$résultats["essayé"] = "true";

		$réussi = true;
		foreach ($tests as $test) {
			$test->réussi = $this->vérifier_solution(
				$test->sorties,
				$test->solution
			);
			if (!$test->réussi) {
				$réussi = false;
			}
		}

		if ($réussi) {
			$avancement->etat = Question::ETAT_REUSSI;
			$avancement->code = $exécutable->code_utilisateur;
			$this->sauvegarder_avancement($avancement);
			$résultats["réussi"] = "true";
		} else {
			if ($avancement->etat != Question::ETAT_REUSSI) {
				$avancement->code = $exécutable->code_utilisateur;
				$avancement->etat = Question::ETAT_NONREUSSI;
				$this->sauvegarder_avancement($avancement);
			}
			$résultats["nonréussi"] = "true";
		}

		$résultats["état_réussi"] = $avancement->etat == Question::ETAT_REUSSI;

		return $résultats;
	}

	private function vérifier_solution($sorties, $solution)
	{
		$sortie_standard = $sorties["output"];

		//en PHP, "" == NULL ( arg!!! )
		return $solution != "null" && $sortie_standard == $solution;
	}

	private function sauvegarder_avancement($avancement)
	{
		$interacteur = new SauvegarderAvancementInt(
			$this->_source,
			$this->_user_id
		);
		$interacteur->sauvegarder($avancement);
	}
}

?>
