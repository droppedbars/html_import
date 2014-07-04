<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-07-01
 * Time: 10:45 PM
 */

namespace html_import\indices;


abstract class IndexSource {
	private $indexPath = null;

	public function __construct($pathToIndex) {
		$this->indexPath = $pathToIndex;
	}

	protected function getIndexPath() {
		return $this->indexPath;
	}

	abstract public function getContents();

} 