<?php

namespace progression\http\contrôleur;

use http\Env\Request;

class JetonCtl
{
    public function post(Request $request, $username){
        $b=$request.body;
        $id=$b.idRessource;
        $type=$b.typeRessource;
        $token= GénérateurDeToken::get_instance()->générerTokenParRessource($username,$id,$type);
        return $this->réponse_json(["Token" => $token]);
    }
}