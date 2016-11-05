<?php
/**
 * File: I.php
 */

namespace Tivnet\WPDB;

use Symfony\Component\Console\Application;

/**
 * Class I: the Instance.
 */
class I {

	/**
	 * @var Config
	 */
	public static $cfg;

	/**
	 * Constructor.
	 */
	public static function init() {
		self::$cfg = new Config();
		self::runApplication();
	}

	/**
	 * Configure and run the application.
	 */
	protected static function runApplication() {
		$app = new Application( Config::APPLICATION_TITLE, '@package_version@' );
		$app->addCommands( array(
			new Command\DumpCommand(),
			new Command\ListDumpsCommand(),
			new Command\HiCommand(),
			new Command\SelfUpdateCommand(),
		) );
		$app->run();
	}
}
