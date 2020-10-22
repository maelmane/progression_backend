<?php

require_once __DIR__.'/question.php';
require_once 'domaine/entités/question_sys.php';

class QuestionSysDAO extends QuestionDAO{

	static function get_question( $id ){
		$question=new QuestionSys( $id );
		QuestionSysDAO::load( $question );
		return $question;
	}

    public static function load( $objet ){
        parent::load( $objet );
        $query=$GLOBALS[ "conn" ]->prepare( 'SELECT question_systeme.solution_courte,
                                                    question_systeme.image,
                                                    question_systeme.user,
                                                    question_systeme.verification
                                             FROM   question_systeme
                                             WHERE  question_systeme.questionID = ?' );

        $query->bind_param( "i", $objet->id );
        $query->execute();
        $query->bind_result( $objet->solution_courte,
                             $objet->image,
                             $objet->user,
                             $objet->verification );
        if( is_null( $query->fetch() ))
            $objet->id=null;
        $query->close();
    }

    public static function save( $objet ){
        if( !$objet->id ){
            $qid=parent::save();
            $query=$GLOBALS[ "conn" ]->prepare( "INSERT INTO question_systeme ( questionID, image, user, verification, reponse )
                                         VALUES( $qid, ?, ?, ?, ? )" );
            $query->bind_param( "ssss",
                                $objet->image,
                                $objet->user,
                                $objet->verification,
                                $objet->reponse );
            $query->execute();
            $query->close();
        }
        else{
            $qid=parent::save();
            $query=$GLOBALS[ "conn" ]->prepare( "UPDATE question_systeme SET image=?, user=?, verification=?, reponse=? WHERE questionID=$objet->id" );
            $query->bind_param( "ssss",
                                $objet->image,
                                $objet->user,
                                $objet->verification,
                                $objet->reponse );
            $query->execute();
            $query->close();
            
        }
        return $qid;
    }
}


?>
