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
	 * Test to see if file exists, looking in the relativePath.  Return true if the file is there, false otherwise.
	 *
	 * @param string $file
	 * @param string $relativePath
	 *
	 * @return bool
	 */
	public function fileExists( $file, $relativePath = '' ) {
		$fullPath = $this->buildFullPath( $file, $relativePath );
		if ( filter_var( $fullPath, FILTER_VALIDATE_URL ) ) { // if URL TODO: check ensure it is proper http or https
			$realPath = $fullPath;

			return \html_import\XMLHelper::url_exists( $realPath ); // TODO: this feels a bit hacky how I'm handling it
		} else { // else if local directory
			$realPath = realpath( $fullPath );

			return file_exists( $realPath );
		}
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
		// TODO: checks for if it is in fact a valid relative Path... ie "/foo" is a root file or directory
		$fullPath = $this->buildFullPath( $file, $relativePath );
		if ( filter_var( $fullPath, FILTER_VALIDATE_URL ) ) { // if URL
			$realPath = $fullPath;
			if ( \html_import\XMLHelper::url_exists( $realPath ) ) { // TODO: this feels a bit hacky how I'm handling it
				return file_get_contents( $realPath );
			} else {
				return null;
			}
		} else { // else if local directory
			$realPath = realpath( $fullPath );
			if ( file_exists( $realPath ) ) {
				return file_get_contents( $realPath );
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
			if ( filter_var( $fullPath, FILTER_VALIDATE_URL ) ) { // if URL // TODO: this feels a bit hacky how I'm handling it
				return $fullPath;
			} else { // else if directory
				$realPath = realpath( $fullPath );

				return $realPath;
			}
		}
	}

	/**
	 * Searches for filename from the relativePath.
	 *
	 * @param string $filename
	 * @param string $relativePath
	 *
	 * @return string
	 */
	public function findFile( $filename, $relativePath = '' ) {
		// TODO: realpath not compatible with URLs
		$fullPath = realpath( $this->localPath . '/' . $relativePath );
		if (!$fullPath) {
			echo 'Error trying to determine the realpath of '. $this->localPath.'/'.$relativePath.'<br>';
			return null;
		}
		$allFiles = @scandir( $fullPath );
		if ($allFiles === false) {
			echo 'Error trying to scan the directory: '. $fullPath.'.<br>';
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