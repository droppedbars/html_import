<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-06-09
 * Time: 8:52 PM
 */

namespace html_import;

/**
 * Class FileHelper
 * Helper functions or handling files
 * @package html_import
 */
class FileHelper {
	/**
	 * Given a directory, deletes it and all subdirectories.
	 * source: http://www.php.net/manual/en/function.rmdir.php#114183
	 *
	 * @param $dir
	 *
	 * @return bool
	 */
	public static function delTree( $dir ) {
		if ( ( is_null( $dir ) ) || ( strcmp( '', trim( $dir ) ) == 0 ) ) {
			return FALSE;
		}
		$files = array_diff( scandir( $dir ), array( '.', '..' ) );
		foreach ( $files as $file ) {
			( is_dir( "$dir/$file" ) && !is_link( $dir ) ) ? self::delTree( "$dir/$file" ) : unlink( "$dir/$file" );
		}

		return rmdir( $dir );
	}
} 