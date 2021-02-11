<?php

namespace progression\http\contrôleur;

use Laravel\Lumen\Routing\Controller as BaseController;

class Contrôleur extends BaseController
{
    protected function réponse_json( $réponse, $code=200 ){
        return response()->json($réponse,
                                $code,
                                ['Content-Type' => 'application/vnd.api+json', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
