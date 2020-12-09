<?php

require_once __DIR__ . '/entite.php';
require_once 'domaine/entités/entite.php';
require_once 'domaine/entités/avancement.php';

class AvancementDao extends EntiteDAO
{
	static function get_avancement($question_id, $user_id)
	{
		$avancement = new Avancement($question_id, $user_id);
		AvancementDAO::load($avancement);
		if (is_null($avancement->etat)) {
			$avancement->etat = QUESTION::ETAT_DEBUT;
		}

		return $avancement;
	}

	protected static function load($objet)
	{
		$query = AvancementDAO::$conn->prepare(
			'SELECT etat FROM avancement WHERE questionID = ? AND userID = ?'
		);
		$query->bind_param("ii", $objet->question_id, $objet->user_id);
		$query->execute();
		$query->bind_result(
			$objet->etat
		);
		$query->fetch();

		$query->close();
	}

	public static function save($objet)
	{
		$query = AvancementDAO::$conn
			->prepare('INSERT INTO avancement ( etat, questionID, userID ) VALUES ( ?, ?, ?, ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ) ');

		$query->bind_param(
			"iiisss",
			$objet->etat,
			$objet->question_id,
			$objet->user_id
		);
		$query->execute();
		$query->close();

		return AvancementDAO::get_avancement(
			$objet->question_id,
			$objet->user_id
		);
	}
}
?>
