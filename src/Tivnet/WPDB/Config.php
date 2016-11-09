<?php
namespace Tivnet\WPDB;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Configuration loader and storage.
 *
 * @package Tivnet\WPDB
 */
class Config {

	/**
	 * The Application title.
	 *
	 * @var string
	 */
	const APPLICATION_TITLE = 'WPDB: WordPress DataBase Manager';

	/**
	 * WPDB configuration filename.
	 *
	 * @var string
	 */
	const CONFIG_FILE_NAME = '.wpdb.json';

	/**
	 * WordPress configuration filename.
	 *
	 * @var string
	 */
	const WP_CONFIG_FILE_NAME = 'wp-config.php';

	/**
	 * The full path to the project root.
	 *
	 * @var string
	 */
	protected $path_project_root = '.';

	/**
	 * Array of configuration variables.
	 *
	 * @var string[]
	 */
	protected $config = array();

	/**
	 * Configuration defaults.
	 *
	 * @var string[]
	 */
	protected static $config_defaults = array(
		'env'          => 'tmp',
		'dir_dump'     => '.wpdb',
		'dir_web_root' => 'public',
		'dump_ext'     => 'sql',
		'dump_prefix'  => 'wpdb',
		'cmd_tail'     => 'tail -1',
		'cmd_xz'       => 'xz -v',
		'ext_xz'       => 'xz',
		'dump_ignore'  => '',
	);

	/**
	 * This will be set to true if any error occurred.
	 *
	 * @var bool
	 */
	public $is_error = false;

	/**
	 * Get a configuration variable value.
	 *
	 * @param string $key The variable name.
	 *
	 * @return string The value. Empty string if not found.
	 */
	public function get( $key ) {
		return isset( $this->config[ $key ] ) ? $this->config[ $key ] : '';
	}

	/**
	 * Set a configuration variable value.
	 *
	 * @param string $key   The variable name.
	 * @param string $value The value. Default is empty string.
	 */
	public function set( $key, $value = '' ) {
		$this->config[ $key ] = $value;
	}


	/**
	 * Config constructor.
	 */
	public function __construct() {
		$this->path_project_root = getcwd();

		$this->config = self::$config_defaults;
		$this->loadConfig();
		$this->loadWPConfig();

		$this->set( 'mysql_authorization', '--login-path=' . $this->get( 'DB_NAME' ) );
	}

	/**
	 * Load configuration parameters from JSON file, if exists.
	 */
	protected function loadConfig() {

		$file_config = implode( '/', array(
			$this->path_project_root,
			self::CONFIG_FILE_NAME,
		) );

		if ( is_file( $file_config ) && is_readable( $file_config ) ) {
			/** @var string[] $config_overwrite */
			$config_overwrite = json_decode( file_get_contents( $file_config ), JSON_OBJECT_AS_ARRAY );
			if ( is_array( $config_overwrite ) ) {
				$this->config = array_merge( $this->config, $config_overwrite );
			}
		}
	}

	/**
	 * Read defines from the WordPress configuration file.
	 */
	protected function loadWPConfig() {

		$file_wp_config = implode( '/', array(
			$this->path_project_root,
			$this->get( 'dir_web_root' ),
			self::WP_CONFIG_FILE_NAME,
		) );

		if ( is_file( $file_wp_config ) && is_readable( $file_wp_config ) ) {
			$lines          = file( $file_wp_config );
			$regex_template = '/define\s*\(\s*["\'](DB_NAME|DB_USER|DB_HOST)["\']\s*,\s*["\'](.+)["\']\s*\)\s*;/';
			foreach ( $lines as $line ) {
				if ( preg_match( $regex_template, $line, $matches ) ) {
					$this->set( $matches[1], $matches[2] );
				}
			}
		} else {

			$this->is_error = true;

			$msg = '[ERROR] WordPress config file not found: ' . $file_wp_config;

			$output = new ConsoleOutput();
			$f      = new FormatterHelper();

			$output->writeln( array(
				$f->formatBlock( self::APPLICATION_TITLE, 'info' ),
				$f->formatBlock( str_repeat( '=', strlen( self::APPLICATION_TITLE ) ), 'info' ),
				$f->formatBlock( $msg, 'error', true ),
			) );

		}
	}
}
