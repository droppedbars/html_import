<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-09-19
 * Time: 8:01 PM
 */

namespace droppedbars\files;

/**
 * Class FileRetriever
 * Base class for getting files from sources.
 * @package droppedbars\files
 */
abstract class FileRetriever {
	/**
	 * Initialize the class with a path to where the files to retrieve reside.
	 * The path must be just the directory and not the end file.
	 *
	 * @param string $path
	 */
	public abstract function __construct( $path );

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
	public abstract function retrieveFileContents( $file, $relativePath = '' );

	/**
	 * Searches for filename from the relativePath.
	 *
	 * @param string $filename
	 * @param string $relativePath
	 *
	 * @return string
	 */
	public abstract function findFile( $filename, $relativePath = '' );

	/**
	 * Returns the full path to the file, using the relativePath if provided
	 *
	 * @param string $file
	 * @param string $relativePath
	 *
	 * @return string
	 */
	public abstract function getFullFilePath( $file, $relativePath = '' );

	/**
	 * Test to see if file exists, looking in the relativePath.  Return true if the file is there, false otherwise.
	 *
	 * @param string $file
	 * @param string $relativePath
	 *
	 * @return bool
	 */
	public abstract function fileExists( $file, $relativePath = '' );

}