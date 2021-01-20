<?php

require_once __DIR__ . '/entite.php';
require_once 'domaine/entités/entite.php';
require_once 'domaine/entités/avancement_prog.php';
require_once 'domaine/entités/reponse_prog.php';

class AvancementProgDAO extends EntiteDAO
{
    static function get_avancement($question_id, $user_id)
    {
        $avancement = new AvancementProg($question_id, $user_id);
        AvancementProgDAO::load($avancement);
        if (is_null($avancement->etat)) {
            $avancement->etat = QUESTION::ETAT_DEBUT;
        }

        return $avancement;
    }

    protected static function load($objet)
    {
        $query = AvancementProgDAO::$conn->prepare(
            'SELECT etat, code, lang, lang_derniere_reponse
             FROM avancement 
             LEFT JOIN avancement_prog 
             ON avancement.questionID = avancement_prog.questionID AND
                avancement.userID = avancement_prog.userID
             LEFT JOIN reponse_prog 
             ON avancement.questionID = reponse_prog.questionID AND
                avancement.userID = reponse_prog.userID
             WHERE avancement.questionID = ? AND avancement.userID = ?'
        );
        $query->bind_param("ii", $objet->question_id, $objet->user_id);
        $query->execute();
        $query->bind_result($objet->etat, $code, $lang, $objet->lang);

        $réponses = [];
        while ($query->fetch()) {
            $réponses[$lang] = new RéponseProg($lang, $code);
        }

        $objet->réponses = $réponses;
        $query->close();
    }

    public static function save($objet)
    {
        AvancementProgDAO::$conn->begin_transaction();
        try {
            $query = AvancementProgDAO::$conn
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

            $query = AvancementProgDAO::$conn
                ->prepare('INSERT INTO avancement_prog ( questionID, userID, lang_derniere_reponse ) VALUES ( ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE lang_derniere_reponse = VALUES( lang_derniere_reponse ) ');

            $query->bind_param(
                "iii",
                $objet->question_id,
                $objet->user_id,
                $objet->lang
            );
            $query->execute();
            $query->close();
            
            $query = AvancementProgDAO::$conn
                ->prepare('INSERT INTO reponse_prog ( questionID, userID, lang, code ) VALUES ( ?, ?, ?, ?  )
                                              ON DUPLICATE KEY UPDATE code=VALUES( code )');
            foreach ($objet->réponses as $réponse) {
                $query->bind_param(
                    "iiis",
                    $objet->question_id,
                    $objet->user_id,
                    $réponse->langid,
                    $réponse->code
                );
                $query->execute();
            }
            $query->close();

            AvancementProgDAO::$conn->commit();
        } catch (mysqli_sql_exception $exception) {
            AvancementProgDAO::$conn->rollback();

            throw $exception;
        }
        return AvancementProgDAO::get_avancement(
            $objet->question_id,
            $objet->user_id
        );
    }
}
?>