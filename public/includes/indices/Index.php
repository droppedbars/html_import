<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-06-29
 * Time: 12:52 PM
 */

namespace html_import\indices;

require_once( dirname( __FILE__ ) . '/../WPMetaConfigs.php' );
require_once( dirname( __FILE__ ) . '/../retriever/FileRetriever.php' );

abstract class Index {
	protected $retriever = null;

	public function __construct(\droppedbars\files\FileRetriever $fileRetriever) {
		$this->retriever = $fileRetriever;
	}

	abstract public function readIndex($indexFile = null);

	/**
	 * @return \html_import\WPMetaConfigs
	 */
	abstract public function getNextFile();

	abstract public function setToFirstFile();

} 