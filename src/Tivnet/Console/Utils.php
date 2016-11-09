<?php
namespace Tivnet\Console;

/**
 * Class Utils
 */
class Utils {

	/**
	 * Resembles `ls -l`.
	 *
	 * @param string $file_path Full path to the file.
	 *
	 * @return string File info.
	 */
	public static function ls_l( $file_path ) {
		$file_path = str_replace( '\\', '/', $file_path );
		return implode( ' ', array(
			$file_path,
			filesize( $file_path ),
			date( 'Y-m-d H:i', filemtime( $file_path ) ),
		) );
	}
}
