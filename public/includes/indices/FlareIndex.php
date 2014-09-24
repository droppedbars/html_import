<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-06-29
 * Time: 12:56 PM
 */

namespace html_import\indices;

use droppedbars\datastructure\LinkedTree;

require_once(dirname( __FILE__ ) . '/Index.php');
require_once(dirname( __FILE__ ) . '/../retriever/FileRetriever.php');


class FlareIndex extends Index {


	public function readIndex($indexFile = null) {
		$tocJS = $this->retriever->findFile('Toc.js'); // this file defines the hierarchy

		$tocContents = $this->retriever->retrieveFileContents($tocJS);

		preg_match('/numchunks:([0-9]*?),/', $tocContents, $numChunksMatch);
		$numChunks = $numChunksMatch[1]; // TODO: deal with multiple chunks
		preg_match("/prefix:'(.*?)',/", $tocContents, $tocMatches);
		$chunkName = $tocMatches[1]; // TODO: handle alternate chunk file names
		preg_match('/^define\((.*)\);$/', $tocContents, $matches);
		$returnValue = preg_replace('/(\\w*):/U', '"$1":', $matches[1], -1, $count);
		$jsonString = str_replace("'", "\"", $returnValue);
		$jsonArray = json_decode($jsonString, true);

		// now to read the actual TOC based on the chunk name
		$chunkFile = $this->retriever->findFile('Toc_Chunk0.js'); // this file defines what each file's details are
		$chunkContents = $this->retriever->retrieveFileContents($chunkFile);

		// TODO: deal with no files
		$fileTree = $this->getFlareFileList($chunkContents);
		$fileOrder = $jsonArray['tree']['n'];

		$this->buildTree($fileOrder, $fileTree, $this->tree);
	}

	/**
	 * Encodes it as a json string, 'path' and 'title'.
	 * @param $fileOrder
	 * @param $fileList
	 * @param	$parentNode
	 *
	 * @return LinkedTree|null
	 */
	protected function buildTree($fileOrder, $fileList, $parentNode = null) {
		$firstNode = null;
		$counter = 0;
		foreach ($fileOrder as $item) {
			$itemIndex = $item['i'];
			$pagePath = $fileList[$itemIndex]['path'];
			$pageTitle = json_decode('"'.$fileList[$itemIndex]['title'].'"'); // converts unicode chars

			$jsonEncoded = json_encode(Array('path' => $pagePath, 'title' => $pageTitle));
			$node = new LinkedTree($jsonEncoded);
			if (!is_null($parentNode)) {
				$parentNode->addChild($node);
			}
			if ($counter == 0) {
				$firstNode = $node;
			}
			if (array_key_exists('n', $item)) {
				$itemChildren = $item['n'];
				$this->buildTree($itemChildren, $fileList, $node);
			}
			$counter++;
		}
		return $firstNode;
	}


	private function getFlareFileList( $tocChunkContents ) {
		$count = null;
		$matches = null;
		preg_match('/^define\((.*)\);$/', $tocChunkContents, $matches);

		$returnValue = preg_replace('/(\\w):([\{\[])/', '"$1":$2', $matches[1], -1, $count);

		$jsonString = str_replace("'", "\"", $returnValue);

		$jsonArray = json_decode($jsonString, true);

		$fileList = Array();
		foreach ($jsonArray as $path => $chunk) {
			$id = $chunk['i'];
			$title = $chunk['t'];
			if (sizeof($id) > 1) {
				foreach ($id as $key => $value) {
					$fileList[$value]['path'] = null;
					$fileList[$value]['title'] = $title[$key];
				}
			} else {
				$fileList[$id[0]]['path'] = $path;
				$fileList[$id[0]]['title'] = $title[0];
			}
		}

		return $fileList;
	}

}