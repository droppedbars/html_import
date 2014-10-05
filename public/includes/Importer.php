<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:28 PM
 */

namespace html_import;


use html_import\indices\WebPage;

require_once( dirname( __FILE__ ) . '/indices/WebPage.php' );

abstract class Importer {

	protected $settings = null;
	protected $stages = null;

	public function __construct(admin\HtmlImportSettings $settings, HTMLImportStages $stages) {
		$this->settings = $settings;
		$this->stages = $stages;
	}

	public function import(WebPage $webPage, WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null){
	 $this->doImport($webPage, $meta, $html_post_lookup, $media_lookup);
	 $this->save($meta);
	}

	abstract protected function doImport(WebPage $webPage, WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null);

	protected function stageParse(WebPage $webPage, ImportStage $stage, WPMetaConfigs $meta, &$other) {
		$stage->process($webPage, $this->stages, $meta, $other);
	}
	protected function save(WPMetaConfigs $meta) {
		return $meta->updateWPPost();
	}
} 