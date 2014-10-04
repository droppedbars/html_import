<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-09-19
 * Time: 8:03 PM
 */

namespace droppedbars\files;

require_once( dirname( __FILE__ ) . '/FileRetriever.php' );

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
		$realPath = realpath($fullPath);
		if (file_exists($realPath)) {
			return file_get_contents($realPath);
		} else {
			return null;
		}
	}

	public function getFullFilePath($file, $relativePath = '') {
		$relativeFile = realPath($relativePath.'/'.$file);
		if ( $relativeFile[0] == '/' ) {
			return $relativeFile;
		} else {
			$fullPath = $this->localPath.'/'.$relativePath.'/'.$file;
			$realPath = realpath($fullPath);
			return $realPath;
		}
	}

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