<?php
/*
	This file is part of Progression.

	Progression is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Progression is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Progression.  If not, see <https://www.gnu.org/licenses/>.
*/

namespace progression\dao\mail;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use progression\domaine\entité\user\User;

class MailUser
{
	public string $name;
	public string|null $email;

	public function __construct(User $user)
	{
		$this->name = $user->username;
		$this->email = $user->courriel;
	}
}

class Expéditeur
{
	function envoyer_validation_courriel(User $user, string $token): void
	{
		try {
			Mail::to(new MailUser($user))->send(new ValidationCourrielMail($user, $token));
			Log::notice("(" . __CLASS__ . ") Courriel de validation envoyé à {$user->courriel} :");
		} catch (\Swift_TransportException $e) {
			Log::notice("(" . __CLASS__ . ") Erreur d'envoi du courriel à {$user->courriel} :" . $e->getMessage());
		} catch (\Swift_RfcComplianceException $e) {
			Log::error("(" . __CLASS__ . ") Erreur d'envoi du courriel à {$user->courriel} :" . $e->getMessage());
		} catch (\Exception $e) {
			Log::error("(" . __CLASS__ . ") Erreur d'envoi du courriel à {$user->courriel} :" . $e->getMessage());
		}
	}
}
