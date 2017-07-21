<?php

require_once('quiz_preambule.php');
$qst=new Question($_GET['ID']);
$qst->load_info();
if(is_null($qst->id)){
        header('Location: index.php?p=accueil');
}

$avcmt=new Avancement($_GET['ID'], $_SESSION['user_id']);

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

$locale='fr_CA.UTF-8';
setlocale(LC_ALL,$locale);

openlog("quiz",LOG_NDELAY, LOG_LOCAL0);

//Si un code a été soumis, l'insére dans la zone de texte, sinon utilise le code par défaut.
if ($_POST['incode']==''){
    if($avcmt->reponse==''){
        $code=$qst->incode;
    }
    else{
        $code=$avcmt->reponse;
    }
}
else{
    $code=$_POST['incode'];
}


//Récupère les paramètres de compilation
$params=$_POST['params'];

//Récupère les entrées à envoyer au programme
$stdin=trim($_POST['stdin']);

//Exécute le setup
eval($qst->setup);

//Exécute le pré-code
if(!is_null($qst->pre_code))
    $qst->pre_code=eval("return \"$qst->pre_code\";");

//Exécute le post-code
if(!is_null($qst->post_code))
    $qst->post_code=eval("return \"$qst->post_code\";");

//Exécute la réponse
if(!is_null($qst->reponse))
    $qst->reponse=eval("return \"$qst->reponse\";");

$lang_id=$qst->lang;

page_header();

echo"

  <html> 
   <head>
    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <script src='/CodeMirror/lib/codemirror.js'></script>
    <link rel='stylesheet' href='/CodeMirror/lib/codemirror.css'>
    <script  src='/CodeMirror/mode/python/python.js'></script>
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
        <pre class='code-wrapper'><code><form method='post' action=''><table style='background-color: white; border-style:solid; border-color:black; border-width:0px; border-spacing: 10px 10px;'> 
     "; 

if ($qst->pre_code != ""){
    echo " <tr>
            <td colspan=2>
              <textarea id='precode' rows='5' cols='80'>$qst->pre_code</textarea>
             </td>
           </tr>";
}

echo "
      <tr>
       <td colspan=2>
        <textarea id='incode' name = 'incode' cols='80'>$code</textarea>
       </td>
      </tr>";

if ($qst->post_code != ""){
    echo " <tr> 
            <td colspan=2>
             <textarea id='postcode' rows='5' cols='80'>$qst->post_code</textarea>
            </td>
           </tr>";
}

echo "
     <tr>
      <td width=50%>
       <h3>Paramètres </h3>
        <textarea id='params' name = 'params' rows='1' style='width:100%'>$qst->params</textarea>
     
      </td>
      <td width=50%>
       <h3>Entrées</h3>
        <textarea id='stdin' name = 'stdin' rows='1' style='width:100%'>$qst->stdin</textarea>
     
      </td>
     </tr>
    </table>
   <input type=submit value='Soumettre' >
  </form>
 </code>
</pre>
";

//Log le code soumis
$com_log=$_SERVER['REMOTE_ADDR']." - " . $_SERVER["PHP_SELF"] . " : ". $qst->incode;
syslog(LOG_INFO, $com_log);

//Compose le code à exécuter
if ($qst->pre_code != ""){ $qst->pre_code = $qst->pre_code . "\n"; }
$code_exec=preg_replace('~\R~u', "\n", $qst->pre_exec. $qst->pre_code .  $code . $qst->post_code);

//post le code à remotecompiler
$url_rc='http://localhost:12380/compile';
$data_rc=array('language' => $GLOBALS['lang_id'], 'code' => $code_exec, 'parameters' => $qst->params, 'stdin' => $qst->stdin);
$options_rc=array('http'=> array(
    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    'method'  => 'POST',
    'content' => http_build_query($data_rc)));

$context  = stream_context_create($options_rc);
$comp_resp=file_get_contents($url_rc, false, $context);
if ($comp_resp === FALSE) {
    $output="Erreur interne: " . $comp_resp;
} 
else{
    $output=trim(json_decode($comp_resp, true)['output']);
}
                                                   
//Affiche le résultat
if( !is_null($qst->reponse)){
    echo "<br>Résultat attendu : <br><pre class='code-wrapper'><code>" . resume($qst->reponse,21) . "</code></pre><br>";
}
echo "<br>Résultat observé : <br><pre class='code-wrapper'><code>" . resume($output,21) . "</code></pre><br>";

$errors=json_decode($comp_resp,true)['errors'];
if($errors!=""){
    echo "<br>Erreurs et avertissements : <br><pre class='code-wrapper'><code>$errors</code></pre><br>";
}

//Vérifie la réponse
echo "<table width=100%><tr><td>";
if ($qst->reponse!=""){    
    if ($output==$qst->reponse){
        //Met la réponse à jour dans l'avancement
        $avcmt->set_reponse(Question::ETAT_REUSSI, $code);
        
        echo "Bravo! " . ($qst->flag=="" ? "":"La clé est «" . $qst->flag . "»</td>");
        if ($suivante!=""){                                                             
            echo "<td align=center><a href='$qst->suivante'>Question suivante</a></td>";
        }
    }
                                                                                      
    else{
        if($avcmt->get_etat()==Question::ETAT_NONREUSSI){
            //Met la réponse à jour dans l'avancement
            $avcmt->set_reponse(Question::ETAT_NONREUSSI, $code);
        }
        echo "Raté! Essayez encore</td>";
    }
}

echo "<td align=right><a href=index.php?p=serie&ID=$qst->serieID>Retour à la liste de questions</a></td></tr></table>
 <script>
    function betterTab(cm) {
      if (cm.somethingSelected()) {
        cm.indentSelection('add');
      } else {
        cm.replaceSelection(cm.getOption('indentWithTabs')? '\t':
          Array(cm.getOption('indentUnit') + 1).join(' '), 'end', '+input');
      }
    }
    ";

if ($qst->pre_code != ""){
    echo " 
      var preeditor = CodeMirror.fromTextArea(document.getElementById('precode'),{
      readOnly: true,
      lineNumbers: true,
      firstLineNumber: " . strval(substr_count($qst->pre_exec, "\n") + 1)  . ",
      indentUnit: 4
      });    
     preeditor.setSize(null,'100%');//preeditor.getScrollInfo().height);
     ";}

echo "
      var editor = CodeMirror.fromTextArea(document.getElementById('incode'),{
      matchBrackets: true,
      lineNumbers: true,
      readOnly: false,
      firstLineNumber: " . strval(substr_count($qst->pre_exec, "\n") + substr_count($qst->pre_code, "\n") + 1) . ",
      indentUnit: 4,
      extraKeys: { Tab: betterTab }
      });
      editor.setSize(700);
     ";

if ($qst->post_code !=""){
    echo "
      var posteditor = CodeMirror.fromTextArea(document.getElementById('postcode'),{
      readOnly: true,
      lineNumbers: true,
      firstLineNumber: " . strval(substr_count($qst->pre_exec, "\n") + substr_count($qst->pre_code, "\n")) . "+editor.doc.lineCount()+1,      
      indentUnit: 4
      });
     

    editor.doc.on('change', function(instance, changeObj){
    posteditor.setOption('firstLineNumber', " . strval(substr_count($qst->pre_exec, "\n") + substr_count($qst->pre_code, "\n")) . "+editor.doc.lineCount());     
    editor.setSize(null, editor.getScrollInfo().height);
    });
      ";}

//Affiche le pied de la question
echo "
    </script>
    </div>
  </body>
</html>
";

?>
