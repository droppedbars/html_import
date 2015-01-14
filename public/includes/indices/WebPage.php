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
require_once( dirname( __FILE__ ) . '/WebPageSettings.php' );


use droppedbars\datastructure\LinkedTree;
use droppedbars\files\FileRetriever;
use html_import\XMLHelper;

/**
 * Class WebPage
 * Represents a webpage and its hierarchical relationships, and maintains how and where to retrieve it from.  Also tracks any settings that were defined for the page.
 * @package html_import\indices
 */
class WebPage extends LinkedTree {
	private $title = null;
	private $content = null;
	private $relativePath = null;
	private $retriever = null;
	private $order = null;
	private $settings = null;
	private $wp_id = null;

	/**
	 * @param FileRetriever   $retriever
	 * @param string          $title
	 * @param string          $relativePath
	 * @param null            $content
	 * @param WebPageSettings $settings
	 *
	 * @exception InvalidArgumentException
	 */
	public function __construct( FileRetriever $retriever, $title, $relativePath, $content = null, WebPageSettings $settings = null ) {
		if ( !is_string( $title ) ) {
			throw new \InvalidArgumentException( "Webpage title must be a string" );
		}
		if ( !is_string( $relativePath ) && !is_null( $relativePath ) ) {
			throw new \InvalidArgumentException( "Relative path of webpage must be null or a string" );
		}
		$this->title        = $title;
		$this->content      = $content;
		$this->relativePath = $relativePath;
		$this->retriever    = $retriever;
		$this->settings     = $settings;
	}

	/**
	 *
	 * @param integer $wp_id
	 */
	public function setWPID( $wp_id ) {
		$this->wp_id = $wp_id;
	}

	/**
	 * Returns the WordPress ID of the web page, or null
	 * @return null|integer
	 */
	public function getWPID() {
		return $this->wp_id;
	}

	/**
	 * Sets the absolute position or order of the webpage.  This is used in Wordpress for how pages are ordered in menus.
	 *
	 * @param int $order
	 *
	 * @exception InvalidArgumentException
	 */
	public function setOrderPosition( $order ) {
		if ( !is_integer( $order ) && ( !is_numeric( $order ) ) ) {
			throw new \InvalidArgumentException( "Webpage order must be an integer" );
		}
		$this->order = (int) $order;
	}

	/**
	 * Returns the absolute order of the webpage.
	 * @return null|int
	 */
	public function getOrderPosition() {
		return $this->order;
	}

	/**
	 * Returns the parent of the current page. Null if there is no parent.
	 * @return WebPage|null
	 */
	public function getParent() {
		return parent::getParent();
	}

	/**
	 * Returns the relative path of this webpage as it relates to the file retriever.
	 * @return string
	 */
	public function getRelativePath() {
		return $this->relativePath;
	}

	/**
	 * Builds up and returns the full path of the webpage, using the retriever as the foundation
	 *
	 * @param string $relativePath
	 *
	 * @return string
	 */
	public function getFullPath( $relativePath = null ) {
		if ( !is_string( $relativePath ) && !is_null( $relativePath ) ) {
			throw new \InvalidArgumentException( "Relative path of webpage must be null or a string" );
		}
		if ( !is_null( $relativePath ) ) {
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
	public function getLinkContents( $relativePath = '' ) {
		if ( !is_string( $relativePath ) ) {
			throw new \InvalidArgumentException( "Relative path of webpage must be a string (may be empty)" );
		}

		return $this->retriever->retrieveFileContents( $relativePath, dirname( $this->relativePath ) );
	}

	/**
	 * Returns the title of the webpage
	 * @return string
	 */
	public function getTitle() {
		$title = json_decode( '"' . $this->title . '"' ); // converts unicode chars
		return $title;
	}

	/**
	 * Returns the content of the webpage as a string.  If needed it will retrieve the content from the real page itself
	 * @return string
	 */
	public function getContent() {
		if ( is_null( $this->content ) ) {
			return $this->retriever->retrieveFileContents( $this->getRelativePath() );
		} else {
			return $this->content;
		}
	}

	/**
	 * Tests to see if the webpage object is in fact a folder.
	 * This is used in some document management systems.
	 * @return bool
	 */
	public function isFolder() {
		$content = $this->content;
		if ( is_null( $content ) ) {
			$content = $this->retriever->retrieveFileContents( $this->getRelativePath() );
		}

		return is_null( $content ) || strlen( $content ) <= 0;
	}

	/**
	 * Returns the settings of the webpage.
	 * @return WebPageSettings|null
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * Returns an array of all links destinations contained in the content of the webpage.
	 * @return array
	 */
	public function getAllLinks() {
		$content      = $this->getContent();
		$contentAsXML = XMLHelper::getXMLObjectFromString( $content );

		return XMLHelper::getAllHRefsFromHTML( $contentAsXML );
	}

} 