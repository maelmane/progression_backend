<?php

require_once('modele.php');

function menu_lang($langid=-1, $defaut=false){
    $ret= "<select id='langid' name='langid' > ";
        
    if($defaut){
        $ret=$ret . "<option value=-1 ".(is_null($langid)?"selected":"") . ">défaut</option>";
    }

    $ret=$ret . "
             <option value=11 ".($langid==11?"selected":"") . ">Bash</option>
             <option value=9 ".($langid==9?"selected":"") . ">C</option>
             <option value=8 ".($langid==8?"selected":"") . ">C++</option>
             <option value=7 ".($langid==7?"selected":"") . ">Go</option>
             <option value=10 ".($langid==10?"selected":"") . ">Java</option>
             <option value=12 ".($langid==12?"selected":"") . ">Perl</option>
             <option value=4 ".($langid==4?"selected":"") . ">PHP</option>
             <option value=0 ".($langid==0?"selected":"") . ">Python 2</option>
             <option value=1 ".($langid==1?"selected":"") . ">Python 3</option>
             <option value=2 ".($langid==2?"selected":"") . ">Ruby</option>
           </select>
";

    return $ret;
}
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.php?p=$_GET[p]&ID=$_GET[ID]");
}

function page_header($titre=null){

    if(is_null($titre))
        $titre = "Quiz python";

    echo "  <html>
            <head>
              <meta charset='utf-8'>
	      <link rel='stylesheet' type='text/css' href='css/style.css'>
            <title>$titre</title>
            </head>
	    <body>
          <section class='main'>
		   <div class='example-wrapper clearfix'>

          <table width=100%><tr><td width=75% style='text-align:right;'><h1>" . ($_SESSION["username"]=="admin"?"<a href='?p=admin'>admin</a>":$_SESSION["username"]) . "</h1>(<a href='logout.php'>déconnexion</a>)</td></tr></table>

     ";
}


function page_footer(){
    echo"
               </div>
             </section>
    	    </body>
	    </html>
        ";


}
?>
