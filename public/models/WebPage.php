<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-03-19
 * Time: 7:52 PM
 */

namespace HtmlImporter\Models;


class WebPage {
	public function __construct(\SimpleXMLElement $htmlSourcePage, $sourceFilePath) {

	}

	private function importToWordPress(Array $relativePages, Array $relativeImages, $isStub = true) {

	}

	public function importStubToWordPress(Array $relativePages, Array $relativeImages) {
		$this->importToWordPress($relativePages, $relativeImages, true);
	}

	public function importFullToWordPress(Array $relativePages, Array $relativeImages) {
		$this->importToWordPress($relativePages, $relativeImages, false);
	}

	public function getAbsolutePath() {

	}

	public function getPath() {

	}

	public function getPostId() {

	}

	public function getImages() {

	}
} 