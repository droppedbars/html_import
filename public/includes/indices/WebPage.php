<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-09-26
 * Time: 6:17 AM
 */

namespace html_import\indices;

require_once( dirname( __FILE__ ) . '/../../../includes/LinkedTree.php' );
require_once( dirname( __FILE__ ) . '/../retriever/FileRetriever.php' );


use droppedbars\datastructure\LinkedTree;
use droppedbars\files\FileRetriever;

// TODO: should I extend or wrap?
class WebPage extends LinkedTree{
	private $title = null;
	private $content = null;
	private $relativePath = null;
	private $retriever = null;

	public function __construct(FileRetriever $retriever, $title, $relativePath, $content = null) {
		$this->title = $title;
		$this->content = $content;
		$this->relativePath = $relativePath;
		$this->retriever = $retriever;
	}

	/**
	 * @return WebPage|LinkedTree|null
	 */
	public function getParent() {
		return parent::getParent();
	}

	/**
	 * @return string
	 */
	public function getRelativePath() {
		return $this->relativePath;
	}

	/**
	 * @param string $relativePath
	 */
	public function getFullPath($relativePath = null) {
		if (is_null($relativePath)) {
			return $this->retriever->getFullFilePath( $relativePath, dirname( $this->relativePath ) );
		} else {
			return $this->retriever->getFullFilePath( $this->relativePath );
		}
	}

	/**
	 * @param $relativePath
	 *
	 * @return mixed
	 */
	public function getLinkContents($relativePath) {
		return $this->retriever->retrieveFileContents($relativePath, dirname($this->relativePath));
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		$title = json_decode('"'.$this->title.'"'); // converts unicode chars
		return $title;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		// TODO: deal with errors from the retriever
		if (is_null($this->content)) {
			return $this->retriever->retrieveFileContents($this->getRelativePath());
		} else {
			return $this->content;
		}
	}

	public function isFolder() {
		$content = $this->content;
		if (is_null($content)) {
			$content = $this->retriever->retrieveFileContents($this->getRelativePath());
		}
		return is_null($content);
	}

} 