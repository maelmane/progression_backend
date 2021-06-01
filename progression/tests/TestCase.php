<?php
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

/**
 * Cette classe doit être utilisée pour les tests qui nécessitent l'application Lumen.
 */
abstract class TestCase extends BaseTestCase
{
	/**
	 * Creates the application.
	 *
	 * @return \Laravel\Lumen\Application
	 */
	public function createApplication()
	{
		return require __DIR__ . "/../app/bootstrap/app.php";
	}
}
