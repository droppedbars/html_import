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
		$fullPath = realPath($this->localPath.'/'.$relativePath.'/'.$file);
		if (file_exists($fullPath)) {
			return file_get_contents($fullPath);
		} else {
			return null;
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