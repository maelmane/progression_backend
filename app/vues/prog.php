<?php

function get_langage(){
    if (isset($_POST['langid'])&&$_POST['langid']!=""){
        $langid=$_POST['langid'];
    }
    else{
        $langid=QuestionProg::PYTHON3;
    }

    return $langid;
}

function get_code($question=null, $avancement=null){
    $code="";
    if (isset($_POST['incode'])&&$_POST['incode']!=""){
        $code=$_POST['incode'];
    }
    else{
        if(!is_null($avancement) && $avancement->reponse!=''){
            $code=$avancement->reponse;
        }
        else{
            if(!is_null($question)){
                $code=$question->incode;
            }
        }
    }
    return $code;
}

function get_params($question=null){
    $params="";
    if(!is_null($question) && $question->params!=""){
        $params=$question->params;
    }
    else{
        if(isset($_POST['params'])){
            $params=$_POST['params'];
        }
    }

    return $params;
}

function get_stdin($question=null){
    $stdin="";
    if(!is_null($question) && $question->stdin!=""){
        $stdin=$question->stdin;
    }
    else{
        if(isset($_POST['stdin'])){
            $stdin=$_POST['stdin'];
        }
    }

    return $stdin;
}

function menu_lang($langid=-1, $defaut=false){
    $ret= "<select id='langid' name='langid' > ";
        
    if($defaut){
        $ret=$ret . "<option value=-1 ".(is_null($langid)?"selected":"") . ">défaut</option>";
    }

    $ret=$ret . "
             <option value=0 ".($langid==0?"selected":"") . ">Python 2</option>
             <option value=1 ".($langid==1?"selected":"") . ">Python 3</option>
             <option value=2 ".($langid==2?"selected":"") . ">Ruby</option>
             <option value=4 ".($langid==4?"selected":"") . ">PHP</option>
             <option value=7 ".($langid==7?"selected":"") . ">Go</option>
             <option value=8 ".($langid==8?"selected":"") . ">C++</option>
             <option value=9 ".($langid==9?"selected":"") . ">C</option>
             <option value=10 ".($langid==10?"selected":"") . ">Java</option>
             <option value=11 ".($langid==11?"selected":"") . ">Bash</option>
             <option value=12 ".($langid==12?"selected":"") . ">Perl</option>
           </select>
";

    return $ret;
}

function prog_header($langid){
    prog_header_ouverture();
    prog_header_inclusions_codemiror();
    prog_header_mode_éditeur($langid);
    prog_header_fermeture();
}

function prog_footer($infos){
    scripts_ajustement_éditeurs_header();
    scripts_ajustement_éditeurs();    
    scripts_ajustement_éditeurs_footer();
}

function scripts_ajustement_éditeurs(){
     if (isset($infos['pre_code']) && $infos['pre_code'] != ""){
         prog_footer_precode($infos);
     }
     
     prog_footer_code($infos);
     
     if (isset($infos['post_code']) && $infos['post_code'] != ""){
         prog_footer_postcode($infos);
     }
}

function exécuter_code($infos){
    loguer_code($infos);

    //Extrait les infos
    $langid=$infos["langid"];
    $pre_exec=$infos["pre_exec"];
    $pre_code=$infos["pre_code"];
    $code=$infos["code"];
    $post_code=$infos["post_code"];    
    $params=$infos["params"];
    $stdin=$infos["stdin"];
.
    //Compose le code à exécuter
    $code_exec=preg_replace('~\R~u', "\n", $pre_exec. $pre_code . "\n" . $code . "\n" . $post_code);

    //post le code à remotecompiler
    $url_rc='http://' . $GLOBALS['config']['compilebox_hote'] . ':' . $GLOBALS['config']['compilebox_port'] .'/compile';
    $data_rc=array('language' => $langid, 'code' => $code_exec, 'parameters' => "\"$params\"", 'stdin' => $stdin);
    $options_rc=array('http'=> array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data_rc)));
    $context=stream_context_create($options_rc);
    $comp_resp=file_get_contents($url_rc, false, $context);

    return $comp_resp;
}

function loguer_code($infos){
    $com_log=$_SERVER['REMOTE_ADDR']." - " . $_SERVER["PHP_SELF"] . " : lang : " . $infos['langid'] . " Code : ". $infos['code'];
    syslog(LOG_INFO, $com_log);
}

function afficher_sorties($sorties){
    if ($sorties === FALSE) {
        $output="Erreur interne. ";
    }
    else{
        $output=extraire_sortie_standard($sorties);
        $erreurs=extraire_sortie_erreur($sorties);
    }
    afficher_sortie_standard($output);
    afficher_sortie_erreur($erreurs);
}

function extraire_sortie_standard($sorties){
    return str_replace("\r","",json_decode($sorties, true)['output']);
}

function extraire_sortie_erreur($sorties){
    return json_decode($sorties, true)['errors'];
}

function afficher_résultats($sortie_standard, $infos, $avancement, $question){
    afficher_résultats_header();
    $url_retour="index.php?p=serie&ID=$question->serie_id";
    $titre_retour="la liste de questions";
    afficher_résultats_retour_arrière($url_retour, $titre_retour);
    
    $réussi=valider_résultats($sortie_standard, $infos['reponse']);
    if($réussi){
        sauvegarder_état_réussi($avancement, $infos['code']);
        afficher_réussite($question->code_validation);
    }
    else{
        sauvegarder_état_échec($avancement, $infos['code']);
        afficher_échec();
    }
    
    afficher_question_suivante($avancement, $question);
    afficher_résultats_footer();
}

function valider_résultats($output, $reponse){
    return $reponse!="null" && $output==$reponse; //en PHP, "" == NULL (arg!!!)
}

function sauvegarder_état_réussi($avancement, $code){
    $avancement->set_reponse($code);
    $avancement->set_etat(Question::ETAT_REUSSI);
}

function sauvegarder_état_échec($avancement, $code){
    //Met la réponse à jour dans l'avancement seulement
    //si la question n'avait pas déjà été réussie
    if($avancement->get_etat()!=Question::ETAT_REUSSI){
        $avancement->set_reponse($code);
        $avancement->set_etat(Question::ETAT_NONREUSSI);
    }
}

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

function prog_header_ouverture(){
    echo"<html> 
          <head>";
}

function prog_header_inclusions_codemiror(){
    echo "    <link rel='stylesheet' type='text/css' href='css/style.css'>
              <script src='./CodeMirror/lib/codemirror.js'></script>
              <link rel='stylesheet' href='./CodeMirror/lib/codemirror.css'>";
}

function prog_header_mode_éditeur($langid){
    if($langid<=QuestionProg::PYTHON3){
        echo "<script src='./CodeMirror/mode/python/python.js'></script>";
    }
    elseif($langid==QuestionProg::CPP || $langid==QuestionProg::JAVA){
        echo "<script src='./CodeMirror/mode/clike/clike.js'></script>";
    }
}

function prog_header_fermeture(){
       echo "    <meta charset='utf-8'>
          </head>";
}

function afficher_intro($titre, $énoncé){
    echo "<body>
             <section class='main'>
                 <div class='example-wrapper clearfix'>
                     <h3>$titre</h3>
                     <br>
                     $énoncé
                     <br>
                     <br>";
}
                             
function afficher_formulaire_header(){
    echo "           <pre class='code-wrapper'>
                         <code>
                             <form method='post' action=''>
                                 <table width=100% style='background-color: white; border-style:solid; border-color:black; border-width:0px; border-spacing: 10px 10px;'> ";
}
function afficher_formulaire_selection_langage($infos){
    echo "
                                     <tr>
                                         <td>
                                             Langage : ".menu_lang($infos['langid']>=0?$infos['langid']:1, false)."
                                         </td>
                                     </tr>";
}

function afficher_formulaire_éditeur_precode($infos){
    if ($infos['pre_code'] != ""){
        echo "                       <tr>
                                         <td colspan=2>
                                             <textarea id='precode' rows='5' >".$infos['pre_code']."</textarea>
                                         </td>
                                     </tr>";
    }
}


function afficher_formulaire_éditeur_code($infos){
    echo "
                                     <tr>
                                         <td colspan=2>
                                             <textarea id='incode' name = 'incode' rows='10' cols='80'>".$infos['code']."</textarea>
                                         </td>
                                     </tr>";
}

function afficher_formulaire_éditeur_postcode($infos){
    if ($infos['post_code'] != ""){
        echo "                       <tr>
                                         <td colspan=2>
                                             <textarea id='postcode' rows='5' >".$infos['post_code']."</textarea>
                                         </td>
                                     </tr>";
    }
}

function afficher_formulaire_champs_params_stdin($infos){
    echo "
                                     <tr>
                                         <td width=50%>
                                             <h3>Paramètres </h3>
                                             <textarea id='params' name = 'params' rows='1' style='width:100%'>".$infos['params']."</textarea>
                                         </td>
                                         <td width=50%>
                                             <h3>Entrées</h3>
                                             <textarea id='stdin' name = 'stdin' rows='1' style='width:100%'>".$infos['stdin']."</textarea>
                                         </td>
                                     </tr>";
}

function afficher_formulaire_footer(){
    echo "
                                 </table>
                                 <input type=submit value='Soumettre' >
                             </form>
                         </code>
                     </pre>";
}

function afficher_sortie_standard($output){
    echo "           <br>Sortie standard : <br><pre class='code-wrapper'><code>".resume($output,21)."</code></pre><br>";
}

function afficher_sortie_erreur($erreurs){
    if($erreurs!=""){
        echo "       <br>Sortie d'erreur : <br><pre class='code-wrapper'><code>$erreurs</code></pre><br>";
    }
}

function afficher_résultats_header(){
    echo "           <table width=100%>";
}

function afficher_résultats_retour_arrière($url_retour, $titre_retour){
    echo "               <tr>
                             <td align=left width=25%>
                                 <a href='url_retour'>↩ Retour à $titre_retour</a>
                             </td>";
}

function afficher_réussite($validation){
    echo "                   <td width=50% align=center>
                                 Bonne réponse!".((!is_null($validation)&&$validation!="")?"
                             </td>
                             <td>
                                 Code de validation : $validation":"")."
                             </td>";
}

function afficher_échec(){
    echo "                   <td width=50% align=center>
                                 Raté! Essayez encore
                             </td>";
}

function afficher_question_suivante($avancement, $question){
    echo "                   <td align=right width=25%>";
    if($avancement->get_etat()==Question::ETAT_REUSSI and !is_null($question->suivante)){
        echo "                   <a href=index.php?p=question_prog&ID=$question->suivante>Question suivante →</a>";
    }
    echo "                   </td>";
}

function afficher_résultats_footer(){
    echo "               </tr>
                     </table>";
}

function scripts_ajustement_éditeurs_header(){
     echo "           <script>
                         function betterTab(cm) {
                           if (cm.somethingSelected()) {
                             cm.indentSelection('add');
                           } else {
                             cm.replaceSelection(cm.getOption('indentWithTabs')? '\t':
                               Array(cm.getOption('indentUnit') + 1).join(' '), 'end', '+input');
                           }
                         }";
}

function prog_footer_precode($infos){
    echo "               var preeditor = CodeMirror.fromTextArea(document.getElementById('precode'),{
                             readOnly: true,
                             lineNumbers: true,
                             firstLineNumber: " . strval(substr_count($infos['pre_exec'], "\n") + 1)  . ",
                             indentUnit: 4
                         });    
                         preeditor.setSize(null,'100%');//preeditor.getScrollInfo().height);";
}

function prog_footer_code($infos){
    echo "               var editor = CodeMirror.fromTextArea(document.getElementById('incode'),{
                             matchBrackets: true,
                             lineNumbers: true,
                             readOnly: false,
                             firstLineNumber: " . strval(substr_count($infos['pre_exec'], "\n") + substr_count($infos['pre_code'], "\n") + 1) . ",
                             indentUnit: 4,
                             scrollbarStyle: null,
                             extraKeys: { Tab: betterTab }
                         });
                         editor.setSize('100%', Math.max(100, editor.getScrollInfo().height));";
}

function prog_footer_postcode($infos){
    echo "
                         var posteditor = CodeMirror.fromTextArea(document.getElementById('postcode'),{
                             readOnly: true,
                             lineNumbers: true,
                             firstLineNumber: " . strval(substr_count($infos['pre_exec'], "\n") + substr_count($infos['pre_code'], "\n")) . "+editor.doc.lineCount()+1,
                             indentUnit: 4
                         });
                         posteditor.setSize(null,'100%');
                         editor.doc.on('change', function(instance, changeObj){
                         posteditor.setOption('firstLineNumber', " . strval(substr_count($infos['pre_exec'], "\n") + substr_count($infos['pre_code'], "\n")) . "+editor.doc.lineCount());     
                         editor.setSize('100%', Math.max(100, editor.getScrollInfo().height));
                         });";
}

function scripts_ajustement_éditeurs_footer(){
     echo "
                      </script>
                  </div>
             </section>";
}                             
                             
?>
