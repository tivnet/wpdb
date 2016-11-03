<?php
namespace Tivnet\WPDB;

/**
 * Class Config
 * @package Tivnet\WPDB
 */
class Config {
	public static $dir_dump = 'dbdump-data';
	public static $dump_ext = 'sql';
	public static $cmd_ls = 'ls -o --time-style long-iso';

	/**
	 * Config constructor.
	 */
	public function __construct() {
		$config = array();

		$file_config = getcwd() . '/.wpdb.json';
		if ( is_readable( $file_config ) ) {
			$config = json_decode( file_get_contents( $file_config ), JSON_OBJECT_AS_ARRAY );
		}

		if ( ! empty( $config['dir_dump'] ) ) {
			self::$dir_dump = $config['dir_dump'];
		}

	}

}
