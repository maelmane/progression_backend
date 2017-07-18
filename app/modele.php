<?php

#error_reporting(0);

function db_init(){
    $cfg=parse_ini_file("db.conf");
    $GLOBALS["config"]=$cfg;
                          
    $GLOBALS["conn"] = new mysqli($cfg["servername"], $cfg["username"], $cfg["password"], $cfg["dbname"]);
    $GLOBALS["errno"]=mysqli_connect_errno();
    $GLOBALS["error"]=mysqli_connect_error(); 
}

function get_themes(){
    if(!isset($GLOBALS["conn"])) db_init();
    $conn=$GLOBALS["conn"];

    $themes=$conn->query('SELECT themeID FROM theme');
    
    $res=array();
    while($theme=$themes->fetch_assoc()['themeID']){
        $t=new Theme($theme);
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
        $this->username=$username;

        parent::__construct();
        $this->load_info();
    }
    
    function load_info(){
        $query= $this->conn->prepare( 'SELECT userID, actif FROM users WHERE username = ?');
        $query->bind_param( "s", $this->username );
        $query->execute();
        $query->bind_result( $this->id, $this->actif );
        $res=$query->fetch();
        $query->close();
    }    

    function creer_user(){
        $query=$this->conn->prepare('INSERT INTO users(username) VALUES (?)');
        $query->bind_param( "s", $this->username);
        $query->execute();
        $query->close();
        return $this->get_user_info($this->username);
    }

    function get_ou_cree_user(){
        $user_info=$this->get_user_info($this->username);
    
        if(!$user_info){
            $user_info=$this->create_user($this->username);
        }

        return $user_info;
    }

}

class Theme extends EntiteBD{

    //Données
    public $nom;
    public $titre;
    public $description;
    
    public function __construct($id, $nom=null, $titre=null, $description=null){
        //$this->username=$username;
        parent::__construct();
        
        $this->id=$id;
        $this->nom=$nom;
        $this->titre=$titre;
        $this->description=$description;
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT nom, titre, description FROM theme WHERE themeID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->nom,  $this->titre, $this->description );
        $query->fetch();
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
        $query=$this->conn->prepare('SELECT serieID, nom, numero, titre, description FROM serie WHERE themeID = ?');
        $query->bind_param("i", $this->id);
        $query->execute();
        $query->bind_result( $id, $nom, $numero, $titre, $description);
        
        $series=array();
        while($query->fetch()){
            $series[] = new Serie($id, $nom, $numero, $titre, $description);
        }

        $query->close();
        return $series;
    }
    
    // function get_serie($id){
    //     $query=$this->conn->prepare('SELECT numero, nom, url, titre, description FROM serie WHERE
    //                                  serieID= ? ');
    //     $query->bind_param( "i", $id);
    //     $query->execute();
    //     $query->bind_result($res);
    //     $query->fetch_object();
    //     $query->close();

    //     return $res; #array("numero"=>$numero, "nom"=>$nom, "url"=>$url, "titre"=>$titre, "description"=>$description);
    // }

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
        $query=$this->conn->prepare('SELECT count(question.questionID) FROM avancement, question, theme, serie WHERE 
                                     avancement.questionID=question.questionID AND 
                                     question.serieID=serie.serieID AND 
                                     serie.themeID= ? AND
                                     avancement.avancement = 1');
        echo $this->conn->error;
        $query->bind_param( "i", $this->id);
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
    public $nom;
    public $titre;
    public $url;
    public $description;
    public $themeID;
    
    public function __construct($id, $nom=null, $numero=null, $titre=null, $description=null){
        parent::__construct();
        
        $this->id=$id;
        $this->nom=$nom;
        $this->numero=$numero;
        $this->titre=$titre;
        $this->description=$description;
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT nom, numero, titre, description, themeID FROM serie WHERE serieID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->nom,  $this->numero, $this->titre, $this->description, $this->themeID );
        $query->fetch();
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

    function get_question_no($no){
        $query=$this->conn->prepare('SELECT question.nom,question.url,question.numero,question.titre FROM question,serie,theme WHERE
                                     question.serieID=serie.serieID AND
                                     serie.themeID=theme.themeID AND
                                     theme.nom = ? AND
                                     serie.nom = ? AND
                                     question.numero=?');
        $query->bind_param( "ssi", $this->theme, $this->serie, $no);
        $query->execute();
        $query->bind_result($nom, $url, $numero, $titre);
        $query->fetch();
        $query->close();

        return array("nom"=>$nom, "url"=>$url, "numero"=>$numero, "titre"=>$titre);
                                     
   }

    function get_questions(){
        $query=$this->conn->prepare('SELECT question.questionID, question.nom,question.numero,question.titre,question.description,question.points
                                     FROM question WHERE
                                     question.serieID = ?');
        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result($id, $nom, $numero, $titre, $description, $points);
        
        $questions=array();
        while($query->fetch()){
            $questions[] = new Question($id, $nom, $numero, $titre, $description, $points);
        }
        $query->close();                                     

        return $questions;
   }

    
    function get_avancement(){
        $query=$this->conn->prepare('SELECT count(question.questionID) FROM avancement, question, serie WHERE 
                                     avancement.questionID=question.questionID AND 
                                     question.serieID = ? AND
                                     avancement.avancement=1');

        $query->bind_param( "i", $serie);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();
        return $res;
    }
    
}

class Question extends EntiteBD{

    //Données
    public $nom;
    public $numero;
    public $titre;
    public $lang;
    public $description;
    public $setup;
    public $enonce;
    public $pre_exec;
    public $pre_code;
    public $code;
    public $post_code;
    public $reponse;
    public $params;
    public $stdin;
    public $points;
    public $serieID;
    
    public function __construct($id, $nom=null, $numero=null, $titre=null, $description=null, $points=null){
        //$this->username=$username;
        parent::__construct();
        
        $this->id=$id;
        $this->nom=$nom;
        $this->numero=$numero;
        $this->titre=$titre;
        $this->description=$description;
        $this->points=$points;
    }

    public function load_info(){
        $query=$this->conn->prepare('SELECT question.nom=null, question.numero, question.titre, question.lang, theme.lang, question.description, setup, enonce, pre_exec, pre_code, code, post_code, reponse, params, stdin, points, question.serieID FROM question, serie, theme WHERE question.serieID=serie.serieID AND serie.themeID=theme.themeID AND questionID = ?');

        $query->bind_param( "i", $this->id);
        $query->execute();
        $query->bind_result( $this->nom, $this->numero, $this->titre, $qlang, $tlang, $this->description, $this->setup, $this->enonce, $this->pre_exec, $this->pre_code, $this->code, $this->post_code, $this->reponse, $this->params, $this->stdin, $this->points, $this->serieID );

        $query->fetch();
        $query->close();

        if(is_null($qlang))
            $this->lang=$tlang;
        else
            $this->lang=$qlang;
    }
}

?>