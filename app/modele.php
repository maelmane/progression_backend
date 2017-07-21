<?php

#error_reporting(0);

function db_init(){
    $cfg=parse_ini_file("db.conf");
    $GLOBALS["config"]=$cfg;
                          
    $GLOBALS["conn"] = new mysqli($cfg["servername"], $cfg["username"], $cfg["password"], $cfg["dbname"]);
    $GLOBALS["conn"]->set_charset("utf8");
    $GLOBALS["errno"]=mysqli_connect_errno();
    $GLOBALS["error"]=mysqli_connect_error(); 
}

function get_themes($user_id){
    if(!isset($GLOBALS["conn"])) db_init();
    $conn=$GLOBALS["conn"];

    $themes=$conn->query('SELECT themeID FROM theme');
    
    $res=array();
    while($theme=$themes->fetch_assoc()['themeID']){
        $t=new Theme($theme, $user_id);
        $t->load_info();
        $res[] = $t;
    }

    return $res;
    
}

class EntiteBD{
    //Données
    public $id;
    public $conn;
    
    public function __construct(){
        if(!isset($GLOBALS["conn"])) db_init();
        $this->conn=$GLOBALS["conn"];
    }
}    

class User extends EntiteBD{
    public $username;
    public $actif;

    public function __construct($username){
        parent::__construct();
        $this->load_info($username);
    }
    
    function load_info($username){
        $query= $this->conn->prepare( 'SELECT username, userID, actif FROM users WHERE username = ?');
        $query->bind_param( "s", $username );
        $query->execute();
        $query->bind_result( $this->username, $this->id, $this->actif );
        $res=$query->fetch();
        $query->close();
    }    

    static function creer_user($username){
        $query=$GLOBALS["conn"]->prepare('INSERT INTO users(username) VALUES (?)');
        $query->bind_param( "s", $username);
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
        $query=$this->conn->prepare('SELECT serieID, numero, titre, description FROM serie WHERE themeID = ?');
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
        echo $this->conn->error;
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
        $query=$this->conn->prepare('SELECT count(question.questionID) FROM question, serie WHERE 
                                     question.serieID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();

        return $res;        
    }

    function get_questions(){
        $query=$this->conn->prepare('SELECT question.questionID, question.numero,question.titre,question.description,question.points, avancement.etat 
                                     FROM question LEFT JOIN avancement ON (
                                     avancement.questionID = question.questionID AND
                                     avancement.userID = ?) WHERE
                                     question.serieID = ?
                                     ORDER BY question.numero');
        $query->bind_param( "ii", $this->user_id, $this->id);
        $query->execute();
        $query->bind_result($id, $numero, $titre, $description, $points, $etat);
        
        $questions=array();
        while($query->fetch()){
            $questions[] = new QuestionInfo($id, $numero, $titre, $description, $points, $etat);
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

class QuestionInfo{
    //Données
    public $id;
    public $numero;
    public $titre;
    public $description;
    public $points;
    public $etat;

    public function __construct($id, $numero, $titre, $description, $points, $etat){
        $this->id=$id;
        $this->numero=$numero;
        $this->titre=$titre;
        $this->description=$description;
        $this->points=$points;
        $this->etat=$etat;
    }
}

class Question extends EntiteBD{

    //Constantes
    const ETAT_CACHE=-1;
    const ETAT_NONREUSSI=0;
    const ETAT_REUSSI=1;
    
    //Données
    public $numero;
    public $titre;
    public $lang;
    public $description;
    public $setup;
    public $enonce;
    public $pre_exec;
    public $pre_code;
    public $incode;
    public $post_code;
    public $reponse;
    public $params;
    public $stdin;
    public $points;
    public $serieID;
    
    public function __construct($id, $numero=null, $titre=null, $description=null, $points=null){
        parent::__construct();
        
        $this->id=$id;
        $this->numero=$numero;
        $this->titre=$titre;
        $this->description=$description;
        $this->points=$points;
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT question.questionID, question.numero, question.titre, question.lang, theme.lang, question.description, setup, enonce, pre_exec, pre_code, incode, post_code, reponse, params, stdin, points, question.serieID FROM question, serie, theme WHERE question.serieID=serie.serieID AND serie.themeID=theme.themeID AND question.questionID = ?');

        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->id, $this->numero, $this->titre, $qlang, $tlang, $this->description, $this->setup, $this->enonce, $this->pre_exec, $this->pre_code, $this->incode, $this->post_code, $this->reponse, $this->params, $this->stdin, $this->points, $this->serieID );
        if(is_null($query->fetch()))
           $this->id=null;
        if(is_null($qlang))
            $this->lang=$tlang;
        else
            $this->lang=$qlang;
        $query->close();
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

    public function set_reponse($etat, $reponse){
        if(is_null($this->reponse)){
            $query=$this->conn->prepare('INSERT INTO avancement SET etat = ?, reponse = ?, questionID = ?, userID = ?');
            $query->bind_param("isii", $etat, $reponse, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
        }
        else{
            $query=$this->conn->prepare('UPDATE avancement SET etat = ?, reponse = ? WHERE questionID = ? AND userID = ?');
            $query->bind_param("isii", $etat, $reponse, $this->questionID, $this->userID);
            $query->execute();
            $query->close();
        }
    }
        
}

?>