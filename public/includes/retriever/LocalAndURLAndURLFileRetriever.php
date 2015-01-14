<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-09-19
 * Time: 8:03 PM
 */

namespace droppedbars\files;

require_once( dirname( __FILE__ ) . '/FileRetriever.php' );
require_once( dirname( __FILE__ ) . '/../XMLHelper.php' );

/**
 * Class LocalAndURLFileRetriever
 * Retrieves files from the local file system or that are available from a URL
 * Only HTTP is tested.
 * @package droppedbars\files
 */
class LocalAndURLFileRetriever extends FileRetriever {
	private $localPath = '';

	/**
	 * Initialize the object with the base path.
	 * Path must be the directory the files will be contained in.
	 *
	 * @param string $path
	 */
	public function __construct( $path ) {
		// TODO: if path is a file, drop the file and just use the path
		if ( !is_null( $path ) ) {
			$this->localPath = $path;
		}
	}

	/**
	 * Test to see if file exists, looking in the relativePath.  Return true if the file is there, false otherwise.
	 *
	 * @param string $file
	 * @param string $relativePath
	 *
	 * @return bool
	 */
	public function fileExists( $file, $relativePath = '' ) {
		$fullPath = $this->buildFullPath( $file, $relativePath );
		if ( filter_var( $fullPath, FILTER_VALIDATE_URL ) ) { // if URL
			if ( ( strpos( $fullPath, 'http://' ) == 0 ) || ( strpos( $fullPath, 'https://' ) == 0 ) ) {
				$realPath = $fullPath;

				return \html_import\XMLHelper::url_exists( $realPath );
			} else {
				echo '*** ' . $fullPath . ' is not an HTTP or HTTP URL.';

				return null;
			}
		} else { // else if local directory
			$realPath = realpath( $fullPath );

			return file_exists( $realPath );
		}
	}

	/**
	 * Build up the full file path based on the file and relative path.
	 *
	 * @param $file
	 * @param $relativePath
	 *
	 * @return string
	 */
	private function buildFullPath( $file, $relativePath ) {
		$fullPath = $this->localPath;
		if ( !is_null( $relativePath ) && ( strlen( $relativePath ) > 0 ) ) {
			$fullPath = $fullPath . '/' . $relativePath;
		}
		if ( !is_null( $file ) && ( strlen( $file ) > 0 ) ) {
			$fullPath = $fullPath . '/' . $file;
		}

		return $fullPath;
	}

	/**
	 * Retrieve the contents file, if provided using the relativePath to the base path used for the class.
	 * Returns the contents as as string.
	 * Assumes the string is a text based file.
	 *
	 * @param string $file
	 * @param string $relativePath
	 *
	 * @return string
	 */
	public function retrieveFileContents( $file, $relativePath = '' ) {
		$fullPath = $this->buildFullPath( $file, $relativePath );
		if ( filter_var( $fullPath, FILTER_VALIDATE_URL ) ) { // if URL
			if ( ( strpos( $fullPath, 'http://' ) == 0 ) || ( strpos( $fullPath, 'https://' ) == 0 ) ) {
				$realPath = $fullPath;
				if ( \html_import\XMLHelper::url_exists( $realPath ) ) {
					$file_get_success = file_get_contents( $realPath );
					if ( $file_get_success === false ) {
						echo '*** ' . $relativePath . ' could not be read, may be non-existent or 0 length.';

						return null;
					} else {
						return $file_get_success;
					}
				} else {
					return null;
				}
			} else {
				echo '*** ' . $fullPath . ' is not an HTTP or HTTP URL.';

				return null;
			}
		} else { // else if local directory
			$realPath = realpath( $fullPath );
			if ( $realPath !== false ) {
				$file_get_success = file_get_contents( $realPath );
				if ( $file_get_success === false ) {
					echo '*** ' . $relativePath . ' could not be read, may be non-existent or 0 length.';

					return null;
				} else {
					return $file_get_success;
				}
			} else {
				return null;
			}
		}
	}

	/**
	 * Returns the full path to the file, using the relativePath if provided
	 *
	 * @param string $file
	 * @param string $relativePath
	 *
	 * @return string
	 */
	public function getFullFilePath( $file, $relativePath = '' ) {
		$relativeFile = realPath( $relativePath . '/' . $file );
		if ( $relativeFile[0] == '/' ) {
			return $relativeFile;
		} else {
			$fullPath = $this->buildFullPath( $file, $relativePath );
			if ( filter_var( $fullPath, FILTER_VALIDATE_URL ) ) { // if URL
				if ( ( strpos( $fullPath, 'http://' ) == 0 ) || ( strpos( $fullPath, 'https://' ) == 0 ) ) {
					return $fullPath;
				} else {
					echo '*** ' . $fullPath . ' is not an HTTP or HTTP URL.';

					return null;
				}
			} else { // else if directory
				$realPath = realpath( $fullPath );

				return $realPath;
			}
		}
	}

	/**
	 * Searches for filename from the relativePath.
	 * TODO: This function is limited to local directories only today, and cannot handle URLs.
	 *
	 * @param string $filename
	 * @param string $relativePath
	 *
	 * @return string
	 */
	public function findFile( $filename, $relativePath = '' ) {
		$fullPath = realpath( $this->localPath . '/' . $relativePath );
		if ( !$fullPath ) {
			echo 'Error trying to determine the realpath of ' . $this->localPath . '/' . $relativePath . '<br>';

			return null;
		}
		$allFiles = @scandir( $fullPath );
		if ( $allFiles === false ) {
			echo 'Error trying to scan the directory: ' . $fullPath . '.<br>';

			return null;
		}
		foreach ( $allFiles as $file ) {
			if ( ( strcmp( $file, '.' ) == 0 ) || ( strcmp( $file, '..' ) ) == 0 ) {
				continue;
			}
			if ( strcmp( $filename, $file ) == 0 ) {
				return $relativePath . '/' . $file;
			}
			if ( is_dir( realpath( $this->buildFullPath( $file, $relativePath ) ) ) ) {
				$foundFile = $this->findFile( $filename, $relativePath . '/' . $file );
				if ( !is_null( $foundFile ) ) {
					return $foundFile;
				}
			}
		}
	}

} 