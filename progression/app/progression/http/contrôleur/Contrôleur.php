<?php

namespace progression\http\contrôleur;

use Laravel\Lumen\Routing\Controller as BaseController;

class Contrôleur extends BaseController
{
    protected function réponseJson( $réponse, $code=200 ){
        return response()->json($réponse,
                                $code,
                                ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
