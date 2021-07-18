<?php

namespace progression\providers;

use Illuminate\Auth\GenericUser;
use Illuminate\Auth\Access\Response;
use progression\domaine\entité\User;

class UserPolicy
{
	function access(GenericUser $user, User $cible)
	{
		return $user && $cible && $user->username == $cible->username
			? Response::allow()
			: Response::deny("Opération interdite.");
	}
}
