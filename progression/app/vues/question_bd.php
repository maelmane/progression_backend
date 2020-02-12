<?php

require_once('prog.php');

page_header();
page_contenu();

function page_contenu(){
    $question=charger_question_ou_terminer();
    $avancement=charger_avancement();

    $infos=récupérer_paramètres($question, $avancement);

    $sorties=connexion_conteneur($infos);
    $infos=array_merge($infos, décoder_réponse($sorties));
    sauvegarder_conteneur($infos);
    
    if(isset($_POST['submit'])){
        $infos=array_merge($infos, traiter_résultats($sorties, $infos, $avancement, $question));
    }

    render_page($infos);

}

function charger_question_ou_terminer(){
    $question=new QuestionBD($_GET['ID']);

    if(is_null($question->id)){
        header('Location: index.php?p=accueil');
    }

    return $question;
}

function charger_avancement(){
    $avancement=new Avancement($_GET['ID'], $_SESSION['user_id']);

    return $avancement;
}

function sauvegarder_conteneur($infos){
    if($infos["cont_id"]!="")
        $infos["avancement"]->set_conteneur($infos["cont_id"]);
}

function connexion_conteneur($infos){
    $url_rc=get_url_compilebox();
    $options_rc=get_options_compilebox($infos);

    $context=stream_context_create($options_rc);
    $comp_resp=file_get_contents($url_rc, false, $context);

    return $comp_resp;
}

function décoder_réponse($réponse){
    $infos_réponse=array();
    
    $infos_réponse["cont_id"]=trim(json_decode($réponse, true)['cont_id']);
    $infos_réponse["cont_ip"]=trim(json_decode($réponse, true)['add_ip']);
    $infos_réponse["cont_port"]=trim(json_decode($réponse, true)['add_port']);
    $infos_réponse["res_validation"]=trim(json_decode($réponse, true)['resultat']);
    $infos_réponse["output"]=trim(json_decode($réponse, true)['output']);
    $infos_réponse["erreurs"]=trim(json_decode($réponse, true)['errors']);

    return $infos_réponse;
}

function get_url_compilebox(){
    return "http://".$GLOBALS['config']['compilebox_hote'].":".$GLOBALS['config']['compilebox_port']."/compile";
}

function get_options_compilebox($infos){
    if($infos['avancement']->get_etat()==Question::ETAT_DEBUT || isset($_POST['reset']) && $_POST['reset']=='Réinitialiser'){
        $data_rc=get_data_nouveau_conteneur($infos);
    }
    else{
        $data_rc=get_data_conteneur($infos);
    }
    
    $options_rc=array('http'=> array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data_rc)));

    return $options_rc;
}

function get_data_nouveau_conteneur($infos){
    return array('language' => 14,
                 'code' => 'reset',
                 'vm_name' => $infos['question']->image,
                 'parameters' => $infos['avancement']->conteneur,
                 'params_conteneur' => $infos['params_conteneur'],
                 'stdin' => '',
                 'user' => $infos['question']->user );
}

function get_data_conteneur($infos){
	//Inutile?
    if(is_null($infos['question']->verification) || $infos['question']->verification==""){
        return array('language' => 14,
                     'code' => construire_validation($infos['question'],
                                                     $infos['$avancement']),
                     'vm_name' => $infos['question']->image,
                     'parameters' => $infos['avancement']->conteneur,
                     'params_conteneur' => $infos['params_conteneur'],
                     'stdin' => '',
                     'user' => $infos['question']->user);
    }
    else{
        return array('language' => 14,
                     'code' => construire_validation($infos['question'],
                                                     $infos['avancement']),
                     'vm_name' => $infos['question']->image,
                     'parameters' => $infos['avancement']->conteneur,
                     'params_conteneur' => $infos['params_conteneur'],
                     'stdin' => '',
                     'user' => $infos['question']->user);
    }
}

function construire_validation($question, $avancement){
    echo $question->verification;
    if(!is_null($question->verification) && $question->verification!=""){
        return str_replace("{reponse}", get_réponse_utilisateur(), $question->verification);
    }
    else{
        return $question->pre_exec ."\n". $question->pre_code ."\n". get_code($question, $avancement) ."\n". $question->post_code;
    }
}

function get_réponse_utilisateur(){
    return isset($_POST['reponse'])?$_POST['reponse']:"";
}

function récupérer_paramètres($question, $avancement){
    require_once('helpers.php');

    $langid=$question->lang;

    eval($question->setup);

    $infos=array(
        "question"=>$question,
        "avancement"=>$avancement,
        "titre"=>$question->titre,
        "langid"=>$langid,
		"pre_exec"=>$question->pre_exec,
		"pre_code"=>$question->pre_code,
        "params"=>$question->user,
        "params_conteneur"=>"-e MYSQL_ALLOW_EMPTY_PASSWORD=yes --tmpfs /var/lib/mysql:rw",
        "code"=>get_code($question, $avancement),
		"post_code"=>$question->post_code,
        "reponse"=>get_réponse_utilisateur(),
        "énoncé"=>str_replace("\r","",eval("return \"$question->enonce\";")),
        "solution"=>str_replace("\r","",eval("return $question->solution;")),
        "solution_courte"=>$question->solution_courte,
        "url_retour"=>"index.php?p=serie&ID=".$question->serieID,
        "titre_retour"=>"la liste de questions",
        "suivante"=>$question->suivante,
        "état_réussi"=>$avancement->get_etat()==Question::ETAT_REUSSI,
        "validation"=>$question->code_validation,
        "mode"=>get_mode($langid),
        "lang_nom"=>LANG_NOMS[$langid],
        "nom_serveur"=>$_SERVER["SERVER_NAME"],
        "url_retour"=>"index.php?p=serie&ID=".$question->serieID,
        "titre_retour"=>"la liste de questions");
    

    return $infos;
}

function compter_lignes($texte){
    if($texte==""){
        return 0;
    }
    else{
        return count(preg_split('/\n/',$texte));
    }
}

function traiter_résultats($sorties, $infos, $avancement, $question){
	if($_POST["submit"]=="" || ($infos["solution_courte"]!="" && $infos["reponse"]=="")) return array();

    $résultats=array();
    $résultats["essayé"]="true";
    
    if(!is_null($question->verification) && $question->verification!=""){
        if(vérifier_validation($infos)){
            $résultats["réussi"]="true";
        }
        else{
            $résultats["nonréussi"]="true";
        }
    }
    else if(!is_null($question->solution_courte) && $question->solution_courte!=""){
        if(vérifier_solution_courte($infos)){
            $résultats["réussi"]="true";
        }
        else{
            $résultats["nonréussi"]="true";
        }
    }
    else{
        if(vérifier_solution($sorties, $infos['solution'])){
            $résultats["réussi"]="true";
        }
        else{
            $résultats["nonréussi"]="true";
        }
    }

    if(isset($résultats["réussi"]))
        sauvegarder_état_réussi($avancement, get_code($question, $avancement), $infos['reponse']);
    else
        sauvegarder_état_échec($avancement, get_code($question, $avancement), $infos['reponse']);
    
    $résultats["état_réussi"]=$avancement->get_etat()==Question::ETAT_REUSSI;

    return $résultats;
}

function vérifier_solution($sorties, $solution){
    $sortie_standard=extraire_sortie_standard($sorties);
    $sortie_erreur=extraire_sortie_erreur($sorties);

    //en PHP, "" == NULL (arg!!!)
    return $solution!="null" && $sortie_standard==$solution;
}

function vérifier_validation($infos){
    $réussi=false;
    
    //validation exécutée
    if($infos['res_validation']!="" && $infos['res_validation']=="valide"){
        return true;            
    }

    return false;
}

function vérifier_solution_courte($infos){    
    //réponse textuelle
    if(!is_null($infos["solution_courte"]) &&
       $infos["solution_courte"]!="" &&
       $infos['reponse']!="" &&
       $infos['reponse']==$infos["solution_courte"]){
        return true;
    }
    return false;
}

function sauvegarder_état_réussi($avancement, $code, $reponse){
    $avancement->set_code($code);
    $avancement->set_reponse($reponse);
    $avancement->set_etat(Question::ETAT_REUSSI);
}

function sauvegarder_état_échec($avancement, $code, $reponse){
    //Met la réponse à jour dans l'avancement seulement
    //si la question n'avait pas déjà été réussie
    if($avancement->get_etat()!=Question::ETAT_REUSSI){
        $avancement->set_code($code);        
        $avancement->set_reponse($reponse);
        $avancement->set_etat(Question::ETAT_NONREUSSI);
    }
}

function sauvegarder_état_non_réussi($avancement, $code){
    $avancement->set_etat(Question::ETAT_NONREUSSI);
}

function render_page($infos){
    $template=$GLOBALS['mustache']->loadTemplate("question_bd");
    echo $template->render($infos);
}

?>
