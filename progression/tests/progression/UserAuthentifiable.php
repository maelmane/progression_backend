<?php
namespace progression;

use progression\domaine\entité\user\User;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Utilisateur authentifiable générique. Utilisé seulement dans les tests
 * pour permettre de contourner l'authentification grâce à «actingAs»
 */
class UserAuthentifiable extends User implements Authenticatable
{
	public function getAuthIdentifierName()
	{
		return "username";
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $username;
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return "";
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return "";
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return "";
	}
}

?>
