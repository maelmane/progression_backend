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
if(isset($_POST['reset']) && $_POST['reset']=='Réinitialiser'){
    $data_rc=array('language' => 13, 'code' => 'reset', 'vm_name' => $qst->image, 'parameters' => $avcmt->conteneur, 'stdin' => '');
    $options_rc=array('http'=> array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data_rc)));
}
else{
    $data_rc=array('language' => 13, 'code' => $qst->verification, 'vm_name' => $qst->image, 'parameters' => $avcmt->conteneur, 'stdin' => '');
    $options_rc=array('http'=> array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data_rc)));
}

$context  = stream_context_create($options_rc);
$comp_resp=file_get_contents($url_rc, false, $context);

$cont_id=trim(json_decode($comp_resp, true)['cont_id']);
$cont_ip=trim(json_decode($comp_resp, true)['add_ip']);
$cont_port=trim(json_decode($comp_resp, true)['add_port']);
$res_validation=trim(json_decode($comp_resp, true)['resultat']);

if($avcmt->get_etat()==Question::ETAT_DEBUT){
    $avcmt->set_etat(Question::ETAT_NONREUSSI);
}
$avcmt->set_conteneur($cont_id);

page_header();

echo"

  <html> 



   <head>
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
        <pre class='code-wrapper'><code><form id='form1' method='post' action=''>
        <table width=100%> 
     "; 

    echo " <tr>
       <tr><td align=right colspan=2><a href='https://$_SERVER[SERVER_NAME]:$cont_port' target=_blank>plein écran <img width=16 src='images/fs.png'></a></td></tr>
       <td colspan=2>
         <div>
         <iframe id=tty width=100% height=350 src='https://$_SERVER[SERVER_NAME]:$cont_port'></iframe>
         </div>
       </td>
       </tr>";
if(!is_null($qst->reponse) && $qst->reponse!="" ){
    echo"
   </table><table style='background-color: white; border-style:solid; border-color:black; border-width:0px; border-spacing: 10px 10px;'>
   <tr><td>
   Réponse: <input type=text name=reponse value='$avcmt->reponse'>
   <input type=submit value='Soumettre'></td>";
}
else{
    echo"
   <tr><td>
   <input type=submit name='submit' value='Valider'></td>";
}
echo " <td  align=right><input type=submit name='reset' value='Réinitialiser' onclick='return confirm(\"Voulez-vous vraiment réinitialiser votre session?\");'>";

echo "</td></tr></table>
      <table width=100%>
      <tr><td>";

//Vérifie la réponse
if(!is_null($qst->reponse) && $qst->reponse!=""){
    if($_POST['reponse']!='')
        if($_POST['reponse']==$qst->reponse){
            echo "Bonne réponse!" . ((!is_null($qst->code_validation)&&trim($qst->code_validation!=""))?"</td><td>Code de validation : $qst->code_validation":"");
            $avcmt->set_etat(Question::ETAT_REUSSI);            
        }
        else{
            echo "Mauvaise réponse!";
        }
}
elseif($res_validation!=""){
    if($res_validation=="valide"){
        echo "Bonne réponse!" . ((!is_null($qst->code_validation)&&trim($qst->code_validation!=""))?"</td><td>Code de validation : $qst->code_validation":"");
        $avcmt->set_etat(Question::ETAT_REUSSI);
    }
    elseif($res_validation=="invalide"){
        echo "Mauvaise réponse!";
    }
    else{
        echo "$res_validation</td></tr><tr></td>" ;
    }
}

echo "<td align=right><a href=index.php?p=serie&ID=$qst->serieID>Retour à la liste de questions</a></td></tr></table>
    </div>
  </body>
</html>
";

?>
