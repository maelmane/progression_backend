<?php

require('quiz_preambule.php');

function resume($in, $lignes_max){
    $lignes=explode("\n", $in);
    $nb_lignes=count($lignes);
    if ($nb_lignes<=$lignes_max){
        return $in;
    }
    else{
        $av=round(($lignes_max-1)/2);
        $ap=floor(($lignes_max-1)/2);
        return implode("\n", array_merge(array_slice($lignes,0,$av),array("..."),array_slice($lignes,-$ap)));
    }		
				    
}

$qst=new QuestionSysteme($_GET['ID']);
$qst->load_info();

$avcmt=new Avancement($_GET['ID'], $_SESSION['user_id']);

$locale='fr_CA.UTF-8';
setlocale(LC_ALL,$locale);

openlog("quiz",LOG_NDELAY, LOG_LOCAL0);

//Crée le conteneur
$url_rc='http://localhost:12380/compile';
$data_rc=array('language' => 15, 'code' => $qst->verification, 'vm_name' => $qst->image, 'parameters' => $avcmt->reponse, 'stdin' => '');
$options_rc=array('http'=> array(
    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    'method'  => 'POST',
    'content' => http_build_query($data_rc)));

$context  = stream_context_create($options_rc);
$comp_resp=file_get_contents($url_rc, false, $context);

$cont_id=trim(json_decode($comp_resp, true)['cont_id']);
$cont_ip=trim(json_decode($comp_resp, true)['add_ip']);
$res_validation=trim(json_decode($comp_resp, true)['resultat']);

$avcmt->set_reponse($cont_id);

page_header();

echo"

  <html> 

       <script type='text/javascript' src='browser/anyterm.js'>
       </script>
       <script type='text/javascript'>
         // To create the terminal, just call create_term.  The paramters are:
         //  - The id of a <div> element that will become the terminal.
         //  - The title.  %h and %v expand to the hostname and Anyterm version.
         //  - The number of rows and columns.
         //  - An optional parameter which is substituted for %p in the command string.
         //  - An optional character set.
         //  - An option number of lines of scrollback (default 0).
       
         // So the following creates an 80x25 terminal with 50 lines of scrollback:

         window.onload=function() {create_term('term','',25,100,'$cont_ip','',50);};
         windew.onunload=null;
       </script>


   <head>
    <link rel='stylesheet' type='text/css' href='browser/anyterm.css'>
    <meta charset='utf-8'>
   </head>
   <body>

     <section class='main'>
      <div class='example-wrapper clearfix'>
       <h3>$qst->titre</h3>
           <br>
           $qst->enonce
           <br>
           <br>
        <pre class='code-wrapper'><code><form method='post' action=''>
        <table style='background-color: white; border-style:solid; border-color:black; border-width:0px; border-spacing: 10px 10px;'> 
     "; 

    echo " <tr>
       <td colspan=2>
          <div id='term'></div>
       </td>
       </tr></table>";
if(!is_null($qst->reponse)){
    echo"
   <table style='background-color: white; border-style:solid; border-color:black; border-width:0px; border-spacing: 10px 10px;'>
   <tr><td>
   Réponse: <input type=text name=reponse value='$avcmt->reponse'>
   <input type=submit value='Soumettre' >";
}
else{
    echo"
   <input type=submit value='Valider' >";
}

echo "</td></tr></table>
      <table width=100%>
      <tr><td>";

//Vérifie la réponse
if(!is_null($qst->reponse)){
    if($_POST['reponse']!='')
        if($_POST['reponse']==$qst->reponse){
            echo "Bonne réponse!";
            $avcmt->set_etat(Question::ETAT_REUSSI);            
        }
        else{
            echo "Mauvaise réponse!";
        }
}
else{
    if($res_validation=="1"){
        echo "Bonne réponse!";
        $avcmt->set_etat(Question::ETAT_REUSSI);
    }
    else{
        echo "Mauvaise réponse!";
    }
}

echo "<td align=right><a href=index.php?p=serie&ID=$qst->serieID>Retour à la liste de questions</a></td></tr></table>
    </div>
  </body>
</html>
";

?>
