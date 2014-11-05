<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-06-29
 * Time: 12:56 PM
 */

namespace html_import\indices;

use droppedbars\files\LocalFileRetriever;
use html_import\XMLHelper;

require_once( dirname( __FILE__ ) . '/WebsiteIndex.php' );
require_once(dirname( __FILE__ ) . '/../retriever/FileRetriever.php');
require_once(dirname( __FILE__ ) . '/WebPage.php');

class CrawlWebsiteIndex extends WebsiteIndex {

	private $defaultIndexFiles = ['', 'index.html', 'index.htm'];

	public function buildHierarchyFromWebsiteIndex($indexFile = null) {
		// 1 . need the index. otherwise try index.html or index.htm
		$siteIndex = $indexFile;
		if (is_null($siteIndex)) {
			for ($i = 0; $i < sizeOf($this->defaultIndexFiles); $i++) {
				if ($this->retriever->fileExists($this->defaultIndexFiles[$i])) {
					$siteIndex = $this->defaultIndexFiles[$i];
					break;
				}
			}
		}
		if (!is_null($siteIndex)) {
			$indexWebPage = new WebPage( $this->retriever, null, $indexFile, null, null );
			$this->trees[$this->retriever->getFullFilePath($indexFile)]  = $indexWebPage;
			$this->buildPageListFromLinks( $indexWebPage );
		} else {
			return null; // TODO error??
		}
	}

	private function isNewLink($path) {

		$pathSplitOnDirs = explode('/', $path);
		$fileName = $pathSplitOnDirs[sizeof($pathSplitOnDirs) - 1];

		/* TODO: test cases
			http://foo.com
			http://foo.com/
			http://foo.com/index.htm
			http://foo.com/index.html
			http://foo.com/bar
			http://foo.com/bar/
			http://foo.com/bar/index.htm
			http://foo.com/bar/index.html

			case sensitivities in domain name?
		 */
		if ((strcmp('index.html', $fileName) == 0) || (strcmp('index.htm', $fileName) == 0)) { // index.html or index.htm
			$matches = null;
			preg_match('/(.*)index.html?$/', $path, $matches);
			$fullPath = $matches[1];
			$fullPath = $this->retriever->getFullFilePath( $fullPath );
			$isNew = !array_key_exists( $fullPath, $this->trees );
			$matches = null;

			preg_match('/(.*)\/index.html?$/', $path, $matches);
			$fullPath = $matches[1]; // TODO: sometimes doesn't exist
			$fullPath = $this->retriever->getFullFilePath( $fullPath );
			$isNew = $isNew || !array_key_exists( $fullPath, $this->trees );
		} else if (sizeof($fileName) == 0) { // http://foo.com/bar/
			$fullPath = $path.'index.html';
			$fullPath = $this->retriever->getFullFilePath( $fullPath );
			$isNew = !array_key_exists( $fullPath, $this->trees );
			$fullPath = $path.'index.htm';
			$fullPath = $this->retriever->getFullFilePath( $fullPath );
			$isNew = $isNew || !array_key_exists( $fullPath, $this->trees );
			$fullPath = substr($path, 0, sizeof($fullPath) - 1);
			$fullPath = $this->retriever->getFullFilePath( $fullPath );
			$isNew = $isNew || !array_key_exists( $fullPath, $this->trees );
		} else if (!preg_match('/\./', $fileName)) { // http://foo.com/bar
			$fullPath = $path.'/index.html';
			$fullPath = $this->retriever->getFullFilePath( $fullPath );
			$isNew = !array_key_exists( $fullPath, $this->trees );
			$fullPath = $path.'/index.htm';
			$fullPath = $this->retriever->getFullFilePath( $fullPath );
			$isNew = $isNew || !array_key_exists( $fullPath, $this->trees );
			$fullPath = $path.'/';
			$fullPath = $this->retriever->getFullFilePath( $fullPath );
			$isNew = $isNew || !array_key_exists( $fullPath, $this->trees );
		} else {
			$fullPath = $this->retriever->getFullFilePath( $path );
			$isNew = !array_key_exists( $fullPath, $this->trees ); // TODO: popped up as an invalid key
		}
		return $isNew;
	}

	private function isLocalLinkAndHTML($path) {
		// TODO: very simplified, doing inverse of checking for URL
		// TODO: should check against base url as well for if it's local
		// test if the link is local
		if (!filter_var($path, FILTER_VALIDATE_URL)) {
			$pathSplitOnDirs = explode('/', $path);
			$fileName = $pathSplitOnDirs[sizeof($pathSplitOnDirs) - 1];
			// test if it ends with no extension (ie bla.h/foo/ or bla.h/foo) or if it ends in .html or .htm
			// ensure it is not an in page link (#top)
			// TODO: deal with external pages with in page links (foo.html#top), drop the #top and read it in
			if ((!preg_match('/^\#/', $fileName)) && (((sizeof($fileName) == 0) || (!preg_match('/\./', $fileName)) || (preg_match('/(?:\.html|\.htm)$/', $fileName))))) {
				$fullURL = $this->retriever->getFullFilePath($path);
				if (XMLHelper::url_exists($fullURL)) {
					return true;
				}
			}
		}
		return false;
	}

	private function buildPageListFromLinks(WebPage $sourcePageOfLinks) {
		// TODO: only handles relative links today, need to handle full urls that are local
		$pageLinks = $sourcePageOfLinks->getAllLinks();

		for ($i = 0; $i < sizeOf($pageLinks); $i++) {
			if ($this->isNewLink($pageLinks[$i])) {
				if ($this->isLocalLinkAndHTML($pageLinks[$i])) { // TODO need to ensure it is an HTML file by reading contents and checking
					$fullPath = $this->retriever->getFullFilePath($pageLinks[$i]); // TODO: this should normalize URLs
					$retriever = new LocalFileRetriever($fullPath); // TODO: this needs to come from a factory so that it is the same type as the parent
					$webPage = new WebPage( $retriever, null, null, null, null );
					$this->trees[$fullPath]  = $webPage ;
					//if (/* TODO: test < maxDepth */ ) {
						$this->buildPageListFromLinks( $webPage );
					//}
				}
			}
		}

	}


}