<?php

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JetonCtl extends Contrôleur
{
    public function post(Request $request, $username){
        $id=$request->input("idRessource");
        $type=$request->input("typeRessource");
        $token= GénérateurDeToken::get_instance()->générerTokenParRessource($username,$type,$id);
        $réponse = $this->préparer_réponse($token);
        Log::debug("JetonCtl.post. Retour : ", [$réponse]);
        return $réponse;
    }
}
