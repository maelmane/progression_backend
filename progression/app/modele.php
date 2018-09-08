<?php

#error_reporting(0);

require_once(__DIR__."/config.php");
db_init();

function db_init(){
    if(!isset($GLOBALS["conn"]))
    {
        create_connection();
        set_errors();
    }
}

function create_connection(){
    $GLOBALS["conn"] = new mysqli($GLOBALS["config"]["servername"],
                                  $GLOBALS["config"]["username"],
                                  $GLOBALS["config"]["password"],
                                  $GLOBALS["config"]["dbname"]);
    $GLOBALS["conn"]->set_charset("utf8");
}

function set_errors(){
    $GLOBALS["errno"]=mysqli_connect_errno();
    $GLOBALS["error"]=mysqli_connect_error();
}

function get_themes(){
    $themes=$GLOBALS["conn"]->query('SELECT themeID FROM theme WHERE themeID>0 ORDER BY ordre');
    
    $res=array();
    while($theme_id=$themes->fetch_assoc()['themeID']){
        $t=new Theme($theme_id);
        $res[] = $t;

    }
    $themes->close();
    return $res;
    
}

function get_users(){
    $users=$GLOBALS["conn"]->query('SELECT username FROM users WHERE actif=1 ORDER BY username');

    $res=array();
    $user=$users->fetch_assoc();
    while(!is_null($user)){
        $res[] = new User($user['username']);
        $user=$users->fetch_assoc();
    }

    $users->close();
    return $res;
}

class Entite{
    public $id;
    public $conn;
    
    public function __construct(){
        $this->conn=$GLOBALS["conn"];
        if(!is_null($this->id)) $this->load_info();
    }
}    

class User extends Entite{
    const ROLE_NORMAL=0;
    const ROLE_ADMIN=1;    
    
    public $username;
    public $actif;
    public $role;
    public $id;    

    public function __construct($id){
        $this->id=$id;
        parent::__construct();
    }
    
    public static function existe($username){
        return !is_null(User::get_user_id($username));
    }

    public static function get_user_id($username){
        $query=$GLOBALS["conn"]->prepare( 'SELECT userID FROM users WHERE username = ?');
        $query->bind_param( "s", $username );
        $query->execute();
        $query->bind_result( $id );
        $res=$query->fetch();
        $query->close();
        return $id;
    }

    public function load_info(){
        $query=$this->conn->prepare( 'SELECT userID, username, actif, role FROM users WHERE userID = ? ');
        $query->bind_param( "i", $this->id);
        $query->execute();
	
        $query->bind_result( $this->id, $this->username, $this->actif, $this->role );
        $res=$query->fetch();
        $query->close();
    }    

    public static function creer_user($username){
        $query=$GLOBALS["conn"]->prepare('INSERT INTO users(username) VALUES (?)');
        $query->bind_param( "s", $username);
        $query->execute();
        $query->close();

        return User::get_user_id($username);
    }


}

class Theme extends Entite{

    //Données
    public $titre;
    public $description;
    
    public function __construct($id){
        $this->id=$id;
        parent::__construct();        
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT themeID, titre, description FROM theme WHERE themeID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->id, $this->titre, $this->description );
        if(is_null($query->fetch()))
            $this->id=null;
        $query->close();
    }
    
    function get_nb_series(){
        $query=$this->conn->prepare('SELECT count(serie.serieID) FROM serie WHERE 
                                     serie.themeID= ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();

        return $res;        
    }

    function get_series(){
        $ids=$this->get_series_id();
        
        $series=array();
        foreach ($ids as $id){
            $series[] = new Serie($id);
        }

        return $series;
    }
   
    function get_series_id(){
        $query=$this->conn->prepare('SELECT serieID FROM serie WHERE
                                     themeID= ? ORDER BY numero');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result($s_id);

        $res=array();
        while($query->fetch()){
            $res[]=$s_id;
        }
        $query->close();

        return $res;
    }

    function get_nb_questions(){
        $query=$this->conn->prepare('SELECT count(question.questionID) FROM question, serie WHERE 
                                     question.serieID = serie.serieID AND
                                     serie.themeID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();
        
        return $res;        
    }
    
    function get_avancement($user_id){
        $query=$this->conn->prepare('SELECT count(question.questionID) FROM avancement, question, serie WHERE 
                                     avancement.questionID=question.questionID AND 
                                     avancement.userID= ? AND 
                                     question.serieID=serie.serieID AND 
                                     serie.themeID= ? AND
                                     avancement.etat = '.Question::ETAT_REUSSI);
        $query->bind_param( "ii", $user_id,$this->id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();

        return $res;
    }
    
    function get_pourcentage_avancement($user_id){
        return floor($this->get_avancement($user_id)/$this->get_nb_questions()*100);
    }
}

class Serie extends Entite{
    public $numero;
    public $titre;
    public $description;
    public $themeID;
    
    public function __construct($id){
        $this->id=$id;
        parent::__construct();
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT serieID, numero, titre, description, themeID FROM serie WHERE serieID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->id, $this->numero, $this->titre, $this->description, $this->themeID );
        if(is_null($query->fetch()))
            $this->id=null;
        $query->close();
    }
    
    function get_nb_questions(){
        $query=$this->conn->prepare('SELECT count(question.questionID) FROM question WHERE 
                                     question.serieID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();

        return $res;        
    }

    function get_questions(){
        $ids=$this->get_questions_ids();
        
        $questions=array();
        foreach($ids as $id){
            $questions[] = new Question($id);
        }

        return $questions;
    }

    function get_questions_ids(){
        $query=$GLOBALS["conn"]->prepare('SELECT question.questionID
                                     FROM question WHERE
                                     question.serieID = ?
                                     ORDER BY question.numero');
    
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result($q_id);

        $res=array();
        while($query->fetch()){
            $res[]=$q_id;
        }
        $query->close();

        return $res;
    }

    function get_avancement($user_id){
        $query=$this->conn->prepare('SELECT count(avancement.etat) FROM avancement, question WHERE 
                                     avancement.questionID=question.questionID AND 
                                     avancement.userID= ? AND 
                                     question.serieID = ? AND
                                     avancement.etat='.Question::ETAT_REUSSI);

        $query->bind_param( "ii", $user_id, $this->id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();
        return $res;
    }

    function get_pourcentage_avancement($user_id){
        return floor($this->get_avancement($user_id)/$this->get_nb_questions()*100);
    }
    
}

class Question extends Entite{
    //Constantes d'état
    const ETAT_CACHE=-1;
    const ETAT_DEBUT=0;
    const ETAT_NONREUSSI=1;
    const ETAT_REUSSI=2;

    //Constantes de type
    const TYPE_PROG=0;
    const TYPE_SYS=1;
    
    //Données
    public $serieID;
    public $numero;
    public $titre;
    public $description;
    public $enonce;
    public $points;
    public $etat;
    public $code_validation;
    public $avancement;
    
    public function __construct($id){
        $this->id=$id;
        parent::__construct();
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT question.questionID,
                                            question.type,
                                            question.serieID as s,
                                            question.numero as n,
                                            (select questionID from question where serieID=s and numero=n+1) as suivante,
                                            question.titre,
                                            question.description,
                                            question.enonce,
                                            question.points,
                                            question.code_validation
                                     FROM question
                                     WHERE question.questionID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->id,
                             $this->type,
                             $this->serieID,
                             $this->numero,
                             $this->suivante,
                             $this->titre,
                             $this->description,
                             $this->enonce,
                             $this->points,
                             $this->code_validation);
        if(is_null($query->fetch()))
            $this->id=null;
        $query->close();
    }

    public function save(){
        if($this->id==-1){
            $query=$this->conn->prepare("INSERT INTO question(type,
                                                              serieID,
                                                              titre,
                                                              description,
                                                              numero,
                                                              enonce,
                                                              points,
                                                              code_validation) 
                                     VALUES( ?, ?, ?, ?, ?, ?, ?, ?)");

            $query->bind_param( "iissisis",
                                $this->type,
                                $this->serieID,
                                $this->titre,
                                $this->description,
                                $this->numero,
                                $this->enonce,
                                $this->points,
                                $this->code_validation );
            $query->execute();
            $query->close();

            $query=$this->conn->prepare("SELECT max(questionID) FROM question");
            $query->execute();
            $query->bind_result( $qid );
            $query->fetch();
            $query->close();

        }
        else{
            $query=$this->conn->prepare("UPDATE question set type=?,
                                                serieID=?,
                                                titre=?,
                                                description=?,
                                                numero=?,
                                                enonce=?,
                                                points=?,
                                                code_validation=? WHERE questionID = ?");

            $query->bind_param( "iissisisi",
                                $this->type,
                                $this->serieID,
                                $this->titre,
                                $this->description,
                                $this->numero,
                                $this->enonce,
                                $this->points,
                                $this->code_validation,
                                $this->id );
            $query->execute();
            $query->close();

            $qid=$this->id;
        }
        
        return $qid;
    }

    public function get_avancement($user_id){
        if (is_null($this->avancement))
            $this->avancement=new Avancement($this->id, $user_id);
        return $this->avancement;
    }
    
}

class QuestionProg extends Question{
 
    const PYTHON3=1;
    const CPP=8;
    const JAVA=10;    

    //Données
    public $lang;
    public $setup;
    public $pre_exec;
    public $pre_code;
    public $incode;
    public $post_code;
    public $reponse;
    public $params;
    public $stdin;
    
    public function load_info(){
        parent::load_info();
	    $query=$this->conn->prepare('SELECT question_prog.lang, 
                                            theme.lang, 
                                            question_prog.setup, 
                                            question_prog.pre_exec, 
                                            question_prog.pre_code, 
                                            question_prog.in_code, 
                                            question_prog.post_code, 
                                            question_prog.reponse, 
                                            question_prog.params, 
                                            question_prog.stdin
                                     FROM question 
                                          JOIN question_prog ON
                                          question.questionID=question_prog.questionID 
                                          JOIN serie ON
                                          question.serieID=serie.serieID 
                                          JOIN theme ON
                                          serie.themeID=theme.themeID
                                     WHERE question.questionID = ?');

        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $qlang,
                             $tlang,
                             $this->setup,
                             $this->pre_exec,
                             $this->pre_code,
                             $this->incode,
                             $this->post_code,
                             $this->reponse,
                             $this->params,
                             $this->stdin
        );
        if(is_null($query->fetch()))
            $this->id=null;
        if(is_null($qlang) || $qlang==-1)
            $this->lang=$tlang;
        else
            $this->lang=$qlang;
        $query->close();
    }

    public function save(){
        $qid=parent::save();
        if($this->id==-1){
            $query=$this->conn->prepare("INSERT INTO question_prog(questionID,
                                                                   lang,
                                                                   setup,
                                                                   pre_exec,
                                                                   pre_code,
                                                                   in_code,
                                                                   post_code,
                                                                   reponse,
                                                                   params,
                                                                   stdin)
                                     VALUES( $qid, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param( "issssssss",
                                $this->lang,
                                $this->setup,
                                $this->pre_exec,
                                $this->pre_code,
                                $this->incode,
                                $this->post_code,
                                $this->reponse,
                                $this->params,
                                $this->stdin);
            $query->execute();
            $query->close();
        }
        else{
            $query=$this->conn->prepare("UPDATE question_prog SET lang=?,
                                                                  setup=?,
                                                                  pre_exec=?,
                                                                  pre_code=?,
                                                                  in_code=?,
                                                                  post_code=?,
                                                                  reponse=?,
                                                                  params=?,
                                                                  stdin=? 
                                         WHERE questionID=$this->id");
            $query->bind_param( "issssssss",
                                $this->lang,
                                $this->setup,
                                $this->pre_exec,
                                $this->pre_code,
                                $this->incode,
                                $this->post_code,
                                $this->reponse,
                                $this->params,
                                $this->stdin);
            $query->execute();
            $query->close();
            
        }
        return $qid;
    }
}

class QuestionSysteme extends Question{

    //Données
    public $image;
    public $user;
    public $verification;
    
    public function load_info(){
        parent::load_info();
        $query=$this->conn->prepare('SELECT question_systeme.reponse,
                                            question_systeme.image,
                                            question_systeme.user,
                                            question_systeme.verification
                                     FROM   question_systeme
                                     WHERE  question_systeme.questionID = ?');

        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->reponse,
                             $this->image,
                             $this->user,
                             $this->verification );
        if(is_null($query->fetch()))
            $this->id=null;
        $query->close();
    }

    public function save(){
        $qid=parent::save();
        if($this->id==-1){
            $query=$this->conn->prepare("INSERT INTO question_systeme (questionID, image, user, verification, reponse)
                                     VALUES( $qid, ?, ?, ?, ?)");
            $query->bind_param( "ssss",
                                $this->image,
                                $this->user,
                                $this->verification,
                                $this->reponse);
            $query->execute();
            $query->close();
        }
        else{
            $query=$this->conn->prepare("UPDATE question_systeme SET image=?, user=?, verification=?, reponse=? WHERE questionID=$this->id");
            $query->bind_param( "ssss",
                                $this->image,
                                $this->user,
                                $this->verification,
                                $this->reponse);
            $query->execute();
            $query->close();
            
        }
        return $qid;
    }
}

class Avancement extends Entite{

    public $userID;
    public $questionID;
    private $etat;
    public $reponse;
    public $conteneur;

    public function __construct($question_id, $user_id){
        parent::__construct();

        $this->questionID = $question_id;
        $this->userID = $user_id;
        
        $this->load_info();
    }
    
    private function load_info(){
        $query=$this->conn->prepare('SELECT etat, reponse, conteneur FROM avancement WHERE questionID = ? AND userID = ?');
        $query->bind_param("ii", $this->questionID, $this->userID);
        $query->execute();
        $query->bind_result($this->etat, $this->reponse, $this->conteneur);
        $query->fetch();

        $query->close();
    }

    public function get_etat(){
        return is_null($this->etat) ? Question::ETAT_DEBUT :  $this->etat;
    }

    public function set_etat($etat){
        if($this->get_etat()==Question::ETAT_DEBUT){
            $query=$this->conn->prepare('INSERT INTO avancement SET etat = ?, questionID = ?, userID = ?');
            $query->bind_param("sii", $etat, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
            $this->load_info();
        }
        else{
            $query=$this->conn->prepare('UPDATE avancement SET etat = ? WHERE questionID = ? AND userID = ?');
            $query->bind_param("isi", $etat, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
            $this->etat=$etat;
        }
    }

    public function set_reponse($reponse){
        if($this->get_etat()==Question::ETAT_DEBUT){
            //État par défaut = ETAT_NONREUSSI
            $query=$this->conn->prepare('INSERT INTO avancement SET etat = 1, reponse = ?, questionID = ?, userID = ?');
            $query->bind_param("sii", $reponse, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
            $this->load_info();
        }
        else{
            $query=$this->conn->prepare('UPDATE avancement SET reponse = ? WHERE questionID = ? AND userID = ?');
            $query->bind_param("sii", $reponse, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
            $this->reponse=$reponse;
        }
    }
    public function set_conteneur($conteneur){
        if($this->get_etat()==Question::ETAT_DEBUT){
            //État par défaut = ETAT_NONREUSSI
            $query=$this->conn->prepare('INSERT INTO avancement SET etat = 1, conteneur = ?, questionID = ?, userID = ?');
            $query->bind_param("sii", $conteneur, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
            $this->load_info();
        }
        else{
            $query=$this->conn->prepare('UPDATE avancement SET conteneur = ? WHERE questionID = ? AND userID = ?');
            $query->bind_param("sii", $conteneur, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
            $this->conteneur=$conteneur;
        }
    }        
}

?>
