<?php
namespace Tivnet\WPDB;

/**
 * Class Config
 * @package Tivnet\WPDB
 */
class Config {

	const APPLICATION_TITLE = 'WPDB: WordPress DataBase Manager';

	/**
	 * @var string
	 */
	const CONFIG_FILE_NAME = '.wpdb.json';

	/**
	 * @var string
	 */
	const WP_CONFIG_FILE_NAME = 'wp-config.php';

	/**
	 * @var string
	 */
	protected $path_project_root = '.';

	/**
	 * @var string[]
	 */
	protected $config = array();

	/**
	 * @var string[]
	 */
	protected static $config_defaults = array(
		'dir_dump'     => 'dbdump-data',
		'dir_web_root' => 'public',
		'dump_ext'     => 'sql',
		'cmd_ls'       => 'ls -o --time-style long-iso',
	);

	/**
	 * @param $key
	 *
	 * @return string
	 */
	public function get( $key ) {
		return isset( $this->config[ $key ] ) ? $this->config[ $key ] : '';
	}


	/**
	 * Config constructor.
	 */
	public function __construct() {
		$this->path_project_root = getcwd();

		$this->config = self::$config_defaults;
		$this->load_config();
		$this->load_wp_config();

//		print_r( $this->config );
	}

	/**
	 * Load configuration parameters from JSON file, if exists.
	 */
	protected function load_config() {

		$file_config = implode( '/', array(
			$this->path_project_root,
			self::CONFIG_FILE_NAME,
		) );

		if ( is_file( $file_config ) && is_readable( $file_config ) ) {
			$config_overwrite = json_decode( file_get_contents( $file_config ), JSON_OBJECT_AS_ARRAY );
			if ( is_array( $config_overwrite ) ) {
				$this->config = array_merge( $this->config, $config_overwrite );
			}
		}
	}

	/**
	 * Read defines from WP config.
	 */
	protected function load_wp_config() {

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
					$this->config[ $matches[1] ] = $matches[2];
				}
			}
		}
	}

}
