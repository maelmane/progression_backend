<?php

require_once __DIR__ . '/entite.php';
require_once 'domaine/entités/entite.php';
require_once 'domaine/entités/avancement_sys.php';

class AvancementSysDAO extends EntiteDAO
{
    static function get_avancement($question_id, $user_id)
    {
        $avancement = new AvancementSys($question_id, $user_id, null, null);
        AvancementSysDAO::load($avancement);
        if (is_null($avancement->etat)) {
            $avancement->etat = QUESTION::ETAT_DEBUT;
        }

        return $avancement;
    }

    protected static function load($objet)
    {
        $query = AvancementSysDAO::$conn->prepare(
            'SELECT etat, reponse, conteneur 
             FROM avancement LEFT JOIN reponse_sys 
             ON avancement.questionID = reponse_sys.questionID AND
                avancement.userID = reponse_sys.userID
             WHERE avancement.questionID = ? AND avancement.userID = ?'
        );
        $query->bind_param("ii", $objet->question_id, $objet->user_id);
        $query->execute();
        $query->bind_result($objet->etat, $objet->reponse, $objet->conteneur);
        $query->fetch();

        $query->close();
    }

    public static function save($objet)
    {
        AvancementSysDAO::$conn->begin_transaction();
        try {
            $query = AvancementSysDAO::$conn
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

            $query = AvancementSysDAO::$conn
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

            AvancementProgDAO::$conn->commit();
        } catch (mysqli_sql_exception $exception) {
            AvancementProgDAO::$conn->rollback();

            throw $exception;
        }
        return AvancementSysDAO::get_avancement(
            $objet->question_id,
            $objet->user_id
        );
    }
}
?>
