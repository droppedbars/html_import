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
require_once( dirname( __FILE__ ) . '/../../../includes/LinkedTree.php' );

abstract class Index {
	protected $retriever = null;
	private $nodeCounter = -1;
	protected $tree = null; // first node is always null


	public function __construct(\droppedbars\files\FileRetriever $fileRetriever) {
		$this->retriever = $fileRetriever;
		$this->tree = new \droppedbars\datastructure\LinkedTree(null);
	}

	/**
	 * CONTRACT: Create a LinkedTree structure of the files and make it the children of the $tree member
	 * TODO: can the contract be enforced via the interface?
	 * @param null $indexFile
	 *
	 * @return mixed
	 */
	abstract public function readIndex($indexFile = null);

	public function setToFirstFile() {
		$this->nodeCounter = -1;
	}

	private function nextNode(\droppedbars\datastructure\LinkedTree $currentNode, $limit, &$counter = 0) {
		$child = $currentNode->headChild();
		while (!is_null($child)) {
			if ($limit == $counter) {
				return $child;
			}
			$counter++;
			$grandChild = $this->nextNode($child, $limit, $counter);
			if (!is_null($grandChild) && ($limit == $counter)) {
				return $grandChild;
			}
			$child = $currentNode->nextChild();
		}
		return null;
	}

	public function getNextFile() {
		$this->nodeCounter++;
		return $this->nextNode($this->tree, $this->nodeCounter);
	}

} 