<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-07-01
 * Time: 10:45 PM
 */

namespace html_import\indices;


abstract class IndexSource {
	private $index = null;
	private $indexPath = null;

	public function __construct(Index $index, $indexPath) {
		$this->index = $index;
		$this->indexPath = $indexPath;
		$this->index->readIndex($this->getContents());
	}

	protected function getIndexPath() {
		return $this->indexPath;
	}

	abstract public function getContents();

	/**
	 * @return \html_import\WPMetaConfigs
	 */
	public function getNextFile() {
		return $this->index->getNextFile();
	}

} 