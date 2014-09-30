<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-09-19
 * Time: 8:01 PM
 */

namespace droppedbars\files;


abstract class FileRetriever {
	public abstract function __construct($path);

	public abstract function retrieveFileContents($file, $relativePath = '');

	public abstract function findFile($filename, $relativePath = '');

	public abstract function getFullFilePath($file, $relativePath = '');

}