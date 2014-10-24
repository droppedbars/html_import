<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-09-19
 * Time: 8:03 PM
 */

namespace droppedbars\files;
// TODO: rename this, it supports URLs now.
require_once( dirname( __FILE__ ) . '/FileRetriever.php' );
require_once( dirname( __FILE__ ) . '/../XMLHelper.php' );

class LocalFileRetriever extends FileRetriever {
	private $localPath = '';

	public function __construct( $path ) {
		if (!is_null($path)) {
			$this->localPath = $path;
		}
	}

	public function retrieveFileContents( $file, $relativePath = '' ) {
		// TODO: checks for if it is in fact a valid relative Path... ie "/foo" is a root file or directory
		$fullPath = $this->localPath.'/'.$relativePath.'/'.$file;
		if (filter_var($fullPath, FILTER_VALIDATE_URL)) { // if URL
			$realPath = $fullPath;
			if (\html_import\XMLHelper::url_exists($realPath)) { // TODO: this feels a bit hacky how I'm handling it
				return file_get_contents($realPath);
			} else {
				return null;
			}
		} else { // else if directory
			$realPath = realpath($fullPath);
			if (file_exists($realPath)) {
				return file_get_contents($realPath);
			} else {
				return null;
			}
		}
	}

	public function getFullFilePath($file, $relativePath = '') {
		$relativeFile = realPath($relativePath.'/'.$file);
		if ( $relativeFile[0] == '/' ) {
			return $relativeFile;
		} else {
			$fullPath = $this->localPath.'/'.$relativePath.'/'.$file;
			if (filter_var($fullPath, FILTER_VALIDATE_URL)) { // if URL // TODO: this feels a bit hacky how I'm handling it
				return $fullPath;
			} else { // else if directory
				$realPath = realpath( $fullPath );
				return $realPath;
			}
		}
	}

	// TODO: realpath not compatible with URLs
	public function findFile($filename, $relativePath = '') {
		$allFiles = scandir(realpath($this->localPath.'/'.$relativePath));
		foreach ($allFiles as $file) {
			if ((strcmp($file, '.') == 0) || (strcmp($file, '..')) == 0) {
				continue;
			}
			if (strcmp($filename, $file) == 0) {
				return $relativePath.'/'.$file;
			}
			if (is_dir(realpath($this->localPath.'/'.$relativePath.'/'.$file))) {
				$foundFile = $this->findFile($filename, $relativePath.'/'.$file);
				if (!is_null($foundFile)) {
					return $foundFile;
				}
			}
		}
	}

} 