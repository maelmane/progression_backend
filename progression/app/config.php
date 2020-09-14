<?php

load_config();
instancier_engine_mustache();

function load_config(){
    if(!isset($GLOBALS["config"])){
        $cfg=parse_ini_file("/etc/quiz.conf");
        $GLOBALS["config"]=$cfg;
    }
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

?>
