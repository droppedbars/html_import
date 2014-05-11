<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:28 PM
 */

namespace html_import;


abstract class Importer {

 public function import(admin\HtmlImportSettings $settings, HTMLImportStages $stages, WPMetaConfigs $meta, $body, &$html_post_lookup = null, &$media_lookup = null){
	 $this->doImport($settings, $stages, $meta, $body, $html_post_lookup, $media_lookup);
	 $this->save($meta);
 }

	abstract protected function doImport(admin\HtmlImportSettings $settings, HTMLImportStages $stages, WPMetaConfigs $meta, $body, &$html_post_lookup = null, &$media_lookup = null);

	protected function stageParse(ImportStage $stage, HTMLImportStages $stages, WPMetaConfigs $meta, $body, &$other) {
		$stage->process($stages, $meta, $body, $other);
	}
	protected function save(WPMetaConfigs $meta) {
		return $meta->updateWPPost();
	}
} 