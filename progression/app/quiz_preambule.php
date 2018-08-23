<?php

require __DIR__ . '/vendor/autoload.php';
require_once('modele.php');
vérifier_user_id();
set_locale();
openlog("quiz",LOG_NDELAY, LOG_LOCAL0);
instancier_engine_mustache();

function vérifier_user_id(){
    if(!isset($_SESSION["user_id"])){
        header("Location: login.php".(isset($_GET[p])?"?p=$_GET[p]&ID=$_GET[ID]":"").(isset($_GET['ID'])?("&ID=".$_GET['ID']):""));
    }
}

function set_locale(){
    $locale=isset($GLOBALS['config']['locale'])?$GLOBALS['config']['locale']:'fr_CA.UTF-8';
    setlocale(LC_ALL,$locale);
}

function instancier_engine_mustache(){
    $GLOBALS['mustache'] = new Mustache_Engine(array(
        'template_class_prefix' => '__MyTemplates_',
        'cache' => dirname(__FILE__).'/tmp/cache/mustache',
        'cache_lambda_templates' => true,
        'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/templates'),
        'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/templates/partials'),
        'escape' => function($value) {
            return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
        },
        'charset' => 'UTF-8',
        'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
        'strict_callables' => true,
        'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
    ));
}

function page_header($titre=null){
    if(is_null($titre))
        $titre = "";
    include 'templates/header.php';
}

?>
