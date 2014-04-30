<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:28 PM
 */

namespace html_import;

class HTMLStubImporter extends Importer{

	function __construct() {
	}
	public function import(HTMLImportStages $stages, WPMetaConfigs $meta, $body) {
		$meta->updateWPPost();
	}
} 