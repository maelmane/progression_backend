<?php

#error_reporting(0);

function db_init(){
    if(!isset($GLOBALS["conn"]))
    {    
    $cfg=parse_ini_file("../db.conf");
    $GLOBALS["config"]=$cfg;
                          
    $GLOBALS["conn"] = new mysqli($cfg["servername"], $cfg["username"], $cfg["password"], $cfg["dbname"]);
    $GLOBALS["conn"]->set_charset("utf8");
    $GLOBALS["errno"]=mysqli_connect_errno();
    $GLOBALS["error"]=mysqli_connect_error();
    }
}

function get_themes($user_id){
    if(!isset($GLOBALS["conn"])) db_init();
    $conn=$GLOBALS["conn"];

    $themes=$conn->query('SELECT themeID FROM theme ORDER BY ordre');
    
    $res=array();
    while($theme=$themes->fetch_assoc()['themeID']){
        $t=new Theme($theme, $user_id);
        $t->load_info();
        $res[] = $t;
    }

    return $res;
    
}

function get_users(){
    if(!isset($GLOBALS["conn"])) db_init();
    $conn=$GLOBALS["conn"];

    $users=$conn->query('SELECT username, userID FROM users WHERE actif=1 ORDER BY username');

    $user=$users->fetch_assoc();
    $res=array();
    while(!is_null($user)){
        $res[] = new User($user['userID'],$user['username']);
        $user=$users->fetch_assoc();
    }

    return $res;
}

class EntiteBD{
    //Données
    public $id;
    public $conn;
    
    public function __construct(){
        db_init();
        $this->conn=$GLOBALS["conn"];
    }
}    

class User extends EntiteBD{
    public $username;
    public $actif;
    public $id;    

    public function __construct($id=null, $username=null){
        parent::__construct();
        $this->id=$id;
        $this->username=$username;
    }

    static function exist($username){
        db_init();
        $query=$GLOBALS["conn"]->prepare( 'SELECT count(*) FROM users WHERE username = ?');
        $query->bind_param( "s", $username );
        $query->execute();
        $query->bind_result( $count );
        $res=$query->fetch();
        $query->close();
        return $count;
    }
    
    function load_info($username, $password){
        $this->username=null;
        $this->id=null;
        $query= $this->conn->prepare( 'SELECT count(*) FROM users WHERE username = ? AND password = ?');
        $query->bind_param( "ss", $username, hash('sha256', $password) );
        $query->execute();
        $query->bind_result( $count );
        $res=$query->fetch();
        $query->close();
        if($count!=1) return false;


        $query= $this->conn->prepare( 'SELECT username, userID, actif FROM users WHERE username = ?');
        $query->bind_param( "s", $username  );
        $query->execute();
        $query->bind_result( $this->username, $this->id, $this->actif );
        $res=$query->fetch();
        $query->close();

        return true;
    }    

    static function creer_user($username, $password){
        db_init();
        $query=$GLOBALS["conn"]->prepare('INSERT INTO users(username, password) VALUES (?, ?)');
        $query->bind_param( "ss", $username, hash('sha256', $password) );
        $query->execute();
        $query->close();
    }

}

class Theme extends EntiteBD{

    //Données
    public $titre;
    public $description;
    
    public function __construct($id, $user_id, $titre=null, $description=null){
        //$this->username=$username;
        parent::__construct();
        
        $this->id=$id;
        $this->user_id=$user_id;
        $this->titre=$titre;
        $this->description=$description;
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

    function get_series_id(){
        $query=$this->conn->prepare('SELECT serieID FROM serie WHERE
                                     themeID= ? ');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result($ids);
        $query->fetch();
        $query->close();

        $res=[];
        foreach($query->fetch_all() as $v){
            $res->append($v);
        }

        return $res;
    }

    function get_series(){
        $query=$this->conn->prepare('SELECT serieID, numero, titre, description FROM serie WHERE themeID = ? ORDER BY numero');
        $query->bind_param("i", $this->id);
        $query->execute();
        $query->bind_result( $id, $numero, $titre, $description);
        
        $series=array();
        while($query->fetch()){
            $series[] = new Serie($id, $this->user_id, $numero, $titre, $description);
        }

        $query->close();
        return $series;
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
    
    function get_avancement(){
        $query=$this->conn->prepare('SELECT count(question.questionID) FROM avancement, question, serie WHERE 
                                     avancement.questionID=question.questionID AND 
                                     avancement.userID= ? AND 
                                     question.serieID=serie.serieID AND 
                                     serie.themeID= ? AND
                                     avancement.etat = 1');
        $query->bind_param( "ii", $this->user_id,$this->id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();

        return $res;
    }

}

class Serie extends EntiteBD{
    //Données
    public $numero;
    public $titre;
    public $description;
    public $themeID;
    
    public function __construct($id, $user_id, $numero=null, $titre=null, $description=null){
        parent::__construct();
        
        $this->id=$id;
        $this->user_id=$user_id;
        $this->numero=$numero;
        $this->titre=$titre;
        $this->description=$description;
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
        $query=$this->conn->prepare('SELECT question.questionID,
                                            question.type,
                                            question.numero,
                                            question.titre,
                                            question.description,
                                            question.points,
                                            avancement.etat 
                                     FROM question LEFT JOIN avancement ON (
                                     avancement.questionID = question.questionID AND
                                     avancement.userID = ?) WHERE
                                     question.serieID = ?
                                     ORDER BY question.numero');
        $query->bind_param( "ii", $this->user_id, $this->id);
        $query->execute();
        $query->bind_result($id, $type, $numero, $titre, $description, $points, $etat);
        $questions=array();

        while($query->fetch()){
            $questions[] = new Question($id, $type, $this->id, $numero, $titre, $description, null, $points, $etat);
        }
        $query->close();                                     

        return $questions;
   }

    
    function get_avancement(){
        $query=$this->conn->prepare('SELECT count(avancement.etat) FROM avancement, question WHERE 
                                     avancement.questionID=question.questionID AND 
                                     avancement.userID= ? AND 
                                     question.serieID = ? AND
                                     avancement.etat=1');

        $query->bind_param( "ii", $this->user_id, $this->id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();
        return $res;
    }
    
}

class Question extends EntiteBD{
    //Constantes d'état
    const ETAT_CACHE=-1;
    const ETAT_NONREUSSI=0;
    const ETAT_REUSSI=1;

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
    
    public function __construct($id, $type=null, $serieID=null, $numero=null, $titre=null, $description=null, $enonce=null, $points=null, $etat=null){
        parent::__construct();
        
        $this->id=$id;
        $this->serieID=$serieID;
        $this->type=$type;
        $this->numero=$numero;
        $this->titre=$titre;
        $this->description=$description;
        $this->enonce=$enonce;
        $this->points=$points;
        $this->etat=$etat;
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT question.questionID,
                                            question.type,
                                            question.serieID,
                                            question.numero,
                                            question.titre,
                                            question.description,
                                            question.enonce,
                                            question.points,
                                            avancement.etat 
                                     FROM question LEFT JOIN avancement ON (
                                          avancement.questionID = question.questionID 
                                          AND avancement.userID = ?) 
                                     WHERE question.questionID = ?
                                     ORDER BY question.numero');
        $query->bind_param( "ii", $this->user_id, $this->id);
        $query->execute();
        $query->bind_result( $this->id, $this->type, $this->serieID, $this->numero, $this->titre, $this->description, $this->enonce, $this->points, $this->etat);
        if(is_null($query->fetch()))
           $this->id=null;
        $query->close();
    }

    public function save(){
        if($this->id==-1){
            $query=$this->conn->prepare("INSERT INTO question(type, serieID, titre, description, numero, enonce, points) 
                                     VALUES( ?, ?, ?, ?, ?, ?, ?)");

            $query->bind_param( "iissisi", $this->type, $this->serieID, $this->titre, $this->description, $this->numero, $this->enonce, $this->points );
            $query->execute();
            $query->close();

            $query=$this->conn->prepare("SELECT max(questionID) FROM question");
            $query->execute();
            $query->bind_result( $qid );
            $query->fetch();
            $query->close();

        }
        else{
            $query=$this->conn->prepare("UPDATE question set type=?, serieID=?, titre=?, description=?, numero=?, enonce=?, points=? WHERE questionID = ?");

            $query->bind_param( "iissisii", $this->type, $this->serieID, $this->titre, $this->description, $this->numero, $this->enonce, $this->points, $this->id );
            $query->execute();
            $query->close();

            $qid=$this->id;
        }
        
        return $qid;
    }

}

class QuestionProg extends Question{
 
    const PYTHON3=1;
    const CPP=8;

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
    
    public function __construct($id, $serieID=null, $numero=null, $titre=null, $description=null, $enonce=null, $reponse=null, $points=null, $lang=-1, $setup=null, $pre_exec=null, $pre_code=null, $incode=null, $post_code=null, $params=null, $stdin=null){
        parent::__construct($id, Question::TYPE_PROG, $serieID, $numero, $titre, $description, $enonce, $points);

        $this->lang=$lang;                          
        $this->setup=$setup;                         
        $this->pre_exec=$pre_exec;                      
        $this->pre_code=$pre_code;                      
        $this->incode=$incode;                        
        $this->post_code=$post_code;
        $this->reponse=$reponse;
        $this->params=$params;                        
        $this->stdin=$stdin;                         
                                        
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT question.questionID, 
                                            question.numero, 
                                            question.titre, 
                                            question.enonce, 
                                            question_prog.lang, 
                                            theme.lang, 
                                            question.description, 
                                            question_prog.setup, 
                                            question_prog.pre_exec, 
                                            question_prog.pre_code, 
                                            question_prog.in_code, 
                                            question_prog.post_code, 
                                            question_prog.reponse, 
                                            question_prog.params, 
                                            question_prog.stdin, 
                                            question.points, 
                                            question.serieID
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
        $query->bind_result( $this->id,
                             $this->numero,
                             $this->titre,
                             $this->enonce,
                             $qlang,
                             $tlang,
                             $this->description,
                             $this->setup,
                             $this->pre_exec,
                             $this->pre_code,
                             $this->incode,
                             $this->post_code,
                             $this->reponse,
                             $this->params,
                             $this->stdin,
                             $this->points,
                             $this->serieID
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
            $query=$this->conn->prepare("INSERT INTO question_prog(questionID, lang, setup, pre_exec, pre_code, in_code, post_code, reponse, params, stdin)
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
            $query=$this->conn->prepare("UPDATE question_prog SET lang=?, setup=?, pre_exec=?, pre_code=?, in_code=?, post_code=?, reponse=?, params=?, stdin=? WHERE questionID=$this->id");
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
    
    public function __construct($id, $serieID=null, $numero=null, $titre=null, $description=null, $enonce=null, $reponse=null, $points=null, $image=null, $user=null, $verification=null){
        parent::__construct($id, Question::TYPE_SYS, $serieID, $numero, $titre, $description, $enonce, $points);
        $this->image=$image;
        $this->user=$user;
        $this->reponse=$reponse;
        $this->verification=$verification;
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT question.questionID, 
                                            question.numero, 
                                            question.titre, 
                                            question.description, 
                                            question.enonce, 
                                            question.points, 
                                            question.serieID,
                                            question_systeme.reponse,
                                            question_systeme.image,
                                            question_systeme.user,
                                            question_systeme.verification
                                     FROM   question
                                     JOIN   question_systeme
                                     ON     question.questionID=question_systeme.questionID
                                     WHERE  question.questionID = ?');

        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->id,
                             $this->numero,
                             $this->titre,
                             $this->description,
                             $this->enonce,
                             $this->points,
                             $this->serieID,
                             $this->reponse,
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
            $query->bind_param( "issss",
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


class Avancement extends EntiteBD{

    public $userID;
    public $questionID;
    private $etat;
    public $reponse;

    public function __construct($question_id, $user_id){
        parent::__construct();

        $this->questionID = $question_id;
        $this->userID = $user_id;

        $query=$this->conn->prepare('SELECT etat, reponse FROM avancement WHERE questionID = ? AND userID = ?');
        $query->bind_param("ii", $this->questionID, $this->userID);
        $query->execute();
        $query->bind_result($this->etat, $this->reponse);
        $query->fetch();

        $query->close();
    }

    public function get_etat(){
        if(is_null($this->etat)) return Question::ETAT_NONREUSSI;
        return $this->etat;
    }

    public function set_etat($etat){
        $query=$this->conn->prepare('UPDATE avancement SET etat = ? WHERE questionID = ? AND userID = ?');
        $query->bind_param("isi", $etat, $this->questionID, $this->userID);
        $query->execute();
        $query->close();
    }

    public function set_reponse($reponse){
        if(is_null($this->reponse)){
            $query=$this->conn->prepare('INSERT INTO avancement SET reponse = ?, questionID = ?, userID = ?');
            $query->bind_param("sii", $reponse, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
        }
        else{
            $query=$this->conn->prepare('UPDATE avancement SET reponse = ? WHERE questionID = ? AND userID = ?');
            $query->bind_param("sii", $reponse, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
        }
    }
        
}

?>
