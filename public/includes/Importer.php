<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:28 PM
 */

namespace html_import;


abstract class Importer {

	protected $settings = null;
	protected $stages = null;

	public function __construct(admin\HtmlImportSettings $settings, HTMLImportStages $stages) {
		$this->settings = $settings;
		$this->$stages = $stages;
	}

	public function import(WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null){
	 $this->doImport($meta, $body, $html_post_lookup, $media_lookup);
	 $this->save($meta);
	}

	abstract protected function doImport(WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null);

	protected function stageParse(ImportStage $stage, WPMetaConfigs $meta, &$other) {
		$stage->process($this->stages, $meta, $other);
	}
	protected function save(WPMetaConfigs $meta) {
		return $meta->updateWPPost();
	}
} 