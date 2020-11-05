<?php

require_once __DIR__ . '/question_prog.php';

class QuestionProgMultiTestCtl extends QuestionProgCtl
{
	public function get_page_infos()
	{
		return array_merge(parent::get_page_infos(), [
			"template" => "question_prog_multitest",
		]);
	}

	protected function récupérer_paramètres()
	{
		$infos = array_merge(parent::récupérer_paramètres(), []);

		return $infos;
	}
}

?>
