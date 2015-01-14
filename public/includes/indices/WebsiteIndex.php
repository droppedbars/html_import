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

/**
 * Class WebsiteIndex
 * Extended to support different index formats of websites.  During construction receives a FileRetriever object which describes how to retrieve files, and then retrieves the index file(s) necessary to describe the hierarchy of the website once readIndex is called.  After the hierarchy is built, getNextFile can be used to retrieve each HTML file in the hierarchy.
 *
 * TODO: improve a way to enforce how the class is extended with regards to building the LinkedTree structure
 *
 * @package html_import\indices
 */
abstract class WebsiteIndex {
	protected $retriever = null;
protected $trees = null;
		private $nodeCounter = - 1; // This is an Array to store trees

	/**
	 * @param \droppedbars\files\FileRetriever $fileRetriever describes the source location of the index file(s)
	 */
	public function __construct( \droppedbars\files\FileRetriever $fileRetriever ) {
		$this->retriever = $fileRetriever;
		$this->trees     = Array();
	}

	/**
	 * Calling this function causes the website hierarchy to be built.  An index file to be used may be passed in by the caller or the function can be overridden and inherently know its own index file.
	 *
	 * The expectation is that the implementation will construct a LinkedTree hierarchy of the website from the index file(s).  That hierarchy will always have a null root that is defined upon object construction in @property $tree.  If this expectation is not fulfilled, the functionality will not operate.
	 *
	 * TODO: decrease the coupling of the child class on the LinkedTree object.
	 *
	 * @param null|string $indexFile index file to build website hierarchy from
	 *
	 * @return null|void
	 */
	abstract public function buildHierarchyFromWebsiteIndex( $indexFile = null );

	/**
	 * Reset the file list so that a call to @function nextFile will return the first HTML file.
	 */
	public function setToFirstFile() {
		$this->nodeCounter = - 1;
	}

	/**
	 * Return a LinkedTree node that contains the reference to the next HTML file indicated by the source index. If there is no next HTML file, return null.
	 * @return \html_import\indices\WebPage|null the next HTML file
	 */
	public function getNextHTMLFile() {
		$this->nodeCounter ++;

		return $this->recurseTreeNodeForNext( null );
	}

	/**
	 * Recurses into the LinkedTree hierarchy to retrieve the next file.  This means iterating through siblings as well as children.  The recursion will always go down the children first before attempting to get the siblings.  So a node N's child will be N+1 and N's sibling would be N+2 in a simple 1 child case.
	 * Returns the desired tree node or null otherwise.
	 *
	 * @param \droppedbars\datastructure\LinkedTree $currentNode the current tree node being recursed
	 * @param int                                   $counter     keeps track of the tree node's file
	 *
	 * @return null|\droppedbars\datastructure\LinkedTree Tree node identified by @param $limit.
	 * @throws \droppedbars\datastructure\ChildPayloadNotLinkedTreeException
	 */
	private function recurseTreeNodeForNext( \droppedbars\datastructure\LinkedTree $currentNode = null, &$counter = 0 ) {
		if ( is_null( $currentNode ) ) {
			$child = null;

			foreach ( $this->trees as $tree ) {
				if ( $counter >= $this->nodeCounter ) {
					return $tree;
				}
				$counter ++;
				$child = $this->recurseTreeNodeForNext( $tree, $counter );
				if ( ( !is_null( $child ) ) && ( $counter >= $this->nodeCounter ) ) {
					return $child;
				}
			}

			return $child;
		} else {
			$child = $currentNode->headChild();
			while ( !is_null( $child ) ) {
				if ( $counter >= $this->nodeCounter ) {
					return $child;
				}
				$counter ++;
				$grandChild = $this->recurseTreeNodeForNext( $child, $counter );
				if ( !is_null( $grandChild ) && ( $counter >= $this->nodeCounter ) ) {
					return $grandChild;
				}
				$child = $currentNode->nextChild();
			}

			return null;
		}
	}
}