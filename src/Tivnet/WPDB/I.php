<?php
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
	 * @var Application
	 */
	public static $app;

	/**
	 * Constructor.
	 */
	public static function init() {
		self::$cfg = new Config();
		if ( ! self::$cfg->is_error ) {
			self::runApplication();
		}
	}

	/**
	 * Configure and run the application.
	 */
	protected static function runApplication() {
		self::$app = new Application( Config::APPLICATION_TITLE, '@package_version@' );
		self::$app->addCommands( array(
			new Command\DumpCommand(),
			new Command\HiCommand(),
			new Command\ListDumpsCommand(),
			new Command\LoadCommand(),
			new Command\SelfUpdateCommand(),
		) );
		self::$app->run();
	}
}
