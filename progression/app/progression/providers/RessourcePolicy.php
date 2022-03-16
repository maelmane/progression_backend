<?php

namespace progression\providers;


use Illuminate\Auth\Access\Response;

class RessourcePolicy
{

    function access($user,$ressource, $ressourceCible)
    {
        return $ressource === $ressourceCible
            ? Response::allow()
            : Response::deny("Opération interdite.");
    }

}