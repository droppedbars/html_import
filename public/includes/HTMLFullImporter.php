<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:29 PM
 */

namespace html_import;


class HTMLFullImporter extends Importer {
	private $htmlImportStage = null;
	private $mediaImportStage = null;
	private $updateLinksImportStage = null;
	private $GDNHeaderFooterStage = null;

	function __construct() {
		$this->htmlImportStage = new ImportHTMLStage();
		$this->mediaImportStage = new MediaImportStage();
		$this->updateLinksImportStage = new UpdateLinksImportStage();
		$this->GDNHeaderFooterStage = new GridDeveloperHeaderFooterImportStage();
	}
	public function import(HTMLImportStages $stages, WPMetaConfigs $meta, $body) {
		$this->htmlImportStage->process($stages, $meta, $body);
		$this->GDNHeaderFooterStage->process($stages, $meta, $meta->getPostContent());
		$this->mediaImportStage->process($stages, $meta, $meta->getPostContent());
		$this->updateLinksImportStage->process($stages, $meta, $meta->getPostArray());

		$meta->updateWPPost();
	}
} 