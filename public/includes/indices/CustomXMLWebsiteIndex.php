<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-06-29
 * Time: 12:56 PM
 */

namespace html_import\indices;

require_once( dirname( __FILE__ ) . '/WebsiteIndex.php' );
require_once( dirname( __FILE__ ) . '/../retriever/FileRetriever.php' );
require_once( dirname( __FILE__ ) . '/WebPage.php' );
require_once( dirname( __FILE__ ) . '/WebPageSettings.php' );

/**
 * Class CustomXMLWebsiteIndex
 * Handles websites that are indexed used a standard XML based index.
 * @package html_import\indices
 */
class CustomXMLWebsiteIndex extends WebsiteIndex {
	const DEFAULT_INDEX_FILE_NAME = 'index.xml';

	/**
	 * Calling this function causes the website hierarchy to be built.  An index file to be used may be passed in by the caller or the function can be overridden and inherently know its own index file.
	 *
	 * The expectation is that the implementation will construct a LinkedTree hierarchy of the website from the index file(s).  The top level will always be an array of LinkedTrees, to handle the case where there is no central top node.
	 *
	 * TODO: decrease the coupling of the child class on the LinkedTree object.
	 *
	 * @param null|string $indexFile index file to build website hierarchy from
	 *
	 * @return null|void
	 */
	public function buildHierarchyFromWebsiteIndex( $indexFile = null ) {
		$indexFileToUse = self::DEFAULT_INDEX_FILE_NAME;
		if ( !is_null( $indexFile ) ) {
			$indexFileToUse = $indexFile;
		}
		if ( \html_import\XMLHelper::valid_xml_file( $this->retriever->getFullFilePath( $indexFileToUse ) ) ) {
			$indexContents = $this->retriever->retrieveFileContents( $indexFileToUse );

			$doc      = new \DOMDocument();
			$xmlValid = @$doc->loadXML( $indexContents, LIBXML_NOBLANKS ); // suppresses errors and handles in if/else
			if ( $xmlValid ) {

				$nodelist = $doc->childNodes;
				for ( $i = 0; $i < $nodelist->length; $i ++ ) {

					$this->readInChildNode( $nodelist->item( $i ) );

				}
			} else {
				echo $indexFileToUse . " was not a valid XML file, error trying to load it.<br>";
			}

		} else {
			echo 'Cannot find file ' . $indexFileToUse . "<br>Current path is " . getcwd() . '<br>';
		}
	}

	/**
	 * Reads the child node along with the settings specified in the index the adds it to the tree.
	 *
	 * @param \DOMNode $node
	 * @param WebPage  $parentPage
	 */
	private function readInChildNode( \DOMNode $node, WebPage $parentPage = null ) {
		$webPage  = $parentPage;
		$settings = new WebPageSettings();

		if ( strcmp( $node->nodeName, 'document' ) == 0 ) {
			$attributes = $node->attributes;
			$title      = null;
			$src        = null;
			$tag        = Array();
			$order      = null;
			$category   = Array();

			if ( isset( $attributes ) ) {
				for ( $i = 0; $i < $attributes->length; $i ++ ) {
					$attribute = $attributes->item( $i )->nodeName;
					switch ( $attribute ) {
						case 'title':
							$title = $attributes->item( $i )->nodeValue;
							break;
						case 'src':
							$src = $attributes->item( $i )->nodeValue;
							if ( $src[0] != '/' ) {
								$src = basename( $this->retriever->getFullFilePath( $src ) );
							}
							break;
						case 'category': // if category is set in XML, then overrides the web settings
							// TODO: should have a setting for if to use xml or web settings
							$category = explode( ',', $attributes->item( $i )->nodeValue );
							break;
//						case 'tag':
//							$tag = explode( ',', $attributes->item( $i )->nodeValue );
//							break;
						case 'order':
							$order = $attributes->item( $i )->nodeValue;
							break;
//						/* future cases
//						case 'overwrite-existing':
//							break;
//						*/
						default:
							break;
					}
				}
			}

			if ( !is_null( $category ) && is_array( $category ) ) {
				foreach ( $category as $index => $cat ) {
					$cat_id = wp_create_category( trim( $cat ) );
					$settings->addCategory( intval( $cat_id ) );
				}
			}
			/*	if ( !is_null( $tag ) && is_array( $tag ) ) {
					foreach ( $tag as $t ) {
						//TODO: support tags
					}
				}*/

			$webPage = new WebPage( $this->retriever, $title, $src, null, $settings );
			$webPage->setOrderPosition( $order );
			if ( is_null( $parentPage ) ) {
				$this->trees[] = $webPage;
			} else {
				$parentPage->addChild( $webPage );
			}

		}
		$children = $node->childNodes;
		if ( isset( $children ) ) {
			for ( $i = 0; $i < $children->length; $i ++ ) {
				$this->readInChildNode( $children->item( $i ), $webPage );
			}
		}

	}
} 