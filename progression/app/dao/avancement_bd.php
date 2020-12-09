<?php

require_once __DIR__ . '/entite.php';
require_once 'domaine/entités/entite.php';
require_once 'domaine/entités/avancement.php';

class AvancementBDDAO extends EntiteDAO
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
		$query = AvancementBDDAO::$conn->prepare(
			'SELECT etat, reponse, code, conteneur FROM avancement WHERE questionID = ? AND userID = ?'
		);
		$query->bind_param("ii", $objet->question_id, $objet->user_id);
		$query->execute();
		$query->bind_result(
			$objet->etat,
			$objet->reponse,
			$objet->code_utilisateur,
			$objet->conteneur
		);
		$query->fetch();

		$query->close();
	}

    public static function save($objet)
    {
        $mysql->begin_transaction();
        try {
            $query = AvancementBDDAO::$conn
                ->prepare('INSERT INTO avancement ( etat, questionID, userID ) VALUES ( ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ) ');

            $query->bind_param(
                "iii",
                $objet->etat,
                $objet->question_id,
                $objet->user_id
            );
            $query->execute();
            $query->close();

            $query = AvancementBDDAO::$conn
                ->prepare('INSERT INTO reponse_sys ( questionID, userID, reponse, conteneur ) VALUES ( ?, ?, ?, ?  )
                                              ON DUPLICATE KEY UPDATE reponse=VALUES( reponse ), conteneur=VALUES( conteneur )');

            $query->bind_param(
                "iiss",
                $objet->question_id,
                $objet->user_id,
                $objet->reponse,
                $objet->conteneur
            );
            $query->execute();
            $query->close();

            $query = AvancementBDDAO::$conn
                ->prepare('INSERT INTO reponse_prog ( questionID, userID, lang, code ) VALUES ( ?, ?, ?, ?  )
                                              ON DUPLICATE KEY UPDATE lang=VALUES( lang ), code=VALUES( code )');

            $query->bind_param(
                "iiss",
                $objet->question_id,
                $objet->user_id,
                "mysql",
                $objet->code
            );
            $query->execute();

            $mysqli->commit();
        } catch (mysqli_sql_exception $exception) {
            $mysqli->rollback();

            throw $exception;
        }
        return AvancementBDDAO::get_avancement(
            $objet->question_id,
            $objet->user_id
        );
    }
}
?>
