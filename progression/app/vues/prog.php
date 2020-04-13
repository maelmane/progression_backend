<?php

const LANG_NOMS=array("Python 2",
                      "Python 3",
                      "Ruby",
                      "",
                      "PHP",
                      "",
                      "",
                      "Go",
                      "C++",
                      "C",
                      "Java",
                      "Bash",
                      "Perl",
                      "SSH",
                      "MySQL");


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
        if(!is_null($avancement) && $avancement->code!=''){
            $code=$avancement->code;
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
             <option value=14 ".($langid==14?"selected":"") . ">MySQL</option>
           </select>
             ";

    return $ret;
}

function get_mode($langid){
    if($langid<=QuestionProg::PYTHON3){
        return "python/python.js";
    }
    elseif($langid==QuestionProg::CPP || $langid==QuestionProg::JAVA){
        return "clike/clike.js";
    }
	elseif($langid==QuestionProg::SQL){
		return "sql/sql.js";
	}
}

function prog_header($langid){
    prog_header_ouverture();
    prog_header_inclusions_codemiror();
    prog_header_mode_éditeur($langid);
    prog_header_fermeture();
}

function prog_footer($infos){
    scripts_ajustement_éditeurs_header();
    scripts_ajustement_éditeurs($infos);    
    scripts_ajustement_éditeurs_footer();
}

function scripts_ajustement_éditeurs($infos){
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

    //Compose le code à exécuter
    $code_exec=preg_replace('~\R~u', "\n", $pre_exec. $pre_code . "\n" . $code . "\n" . $post_code);

    //post le code à remotecompiler
    $url_rc='http://' . $GLOBALS['config']['compilebox_hote'] . ':' . $GLOBALS['config']['compilebox_port'] .'/compile';
    $data_rc=array('language' => $langid, 'code' => $code_exec, 'parameters' => "\"$params\"", 'stdin' => $stdin, 'vm_name' => 'remotecompiler');
    $options_rc=array('http'=> array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data_rc)));
    $context=stream_context_create($options_rc);
    $comp_resp=file_get_contents($url_rc, false, $context);

    return $comp_resp;
}

function calculer_sorties($sorties, $infos){
    if ($sorties === FALSE) {
        $output="";
        $erreurs="Erreur interne. ";        
    }
    else{
        $output=extraire_sortie_standard($sorties);
        $erreurs=extraire_sortie_erreur($sorties);
    }

    return array("output"=>$output,
                 "erreurs"=>$erreurs);
}

function loguer_code($infos){
    $com_log=$_SERVER['REMOTE_ADDR']." - " . $_SERVER["PHP_SELF"] . " : lang : " . $infos['langid'] . " Code : ". $infos['code'];
    syslog(LOG_INFO, $com_log);
}

function extraire_sortie_standard($sorties){
    return str_replace("\r","",json_decode($sorties, true)['output']);
}

function extraire_sortie_erreur($sorties){
    return json_decode($sorties, true)['errors'];
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
