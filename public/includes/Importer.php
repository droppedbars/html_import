<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:28 PM
 */

namespace html_import;


abstract class Importer {

	abstract public function import(HTMLImportStages $stages, WPMetaConfigs $meta, $body);
	protected function stageParse(ImportStage $stage, HTMLImportStages $stages, WPMetaConfigs $meta, $body) {
		$stage->process($stages, $meta, $body);
	}
	protected function save(WPMetaConfigs $meta) {
		return $meta->updateWPPost();
	}
} 