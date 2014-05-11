<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:29 PM
 */

namespace html_import;

require_once( dirname( __FILE__ ) . '/Importer.php' );

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
		$this->setTemplateStage = new SetTemplateStage();
	}
	protected function doImport(admin\HtmlImportSettings $settings, HTMLImportStages $stages, WPMetaConfigs $meta, $body, &$html_post_lookup = null, &$media_lookup = null) {
		$meta->setPostContent($body);

		$this->stageParse($this->htmlImportStage, $stages, $meta, $meta->getPostContent(), $nothing = null);
		$this->stageParse($this->GDNHeaderFooterStage, $stages, $meta, $meta->getPostContent(), $nothing = null);
		$this->stageParse($this->updateLinksImportStage, $stages, $meta, $meta->getPostContent(), $html_post_lookup);
		$this->stageParse($this->mediaImportStage, $stages, $meta, $meta->getPostContent(), $media_lookup);

		$meta->updateWPPost();  // this happens automatically at the end, but needs to happen here to guarantee an ID for the template update

		$this->stageParse($this->setTemplateStage, $stages, $meta, $meta->getPostContent(), $nothing = null);

		$updateResult = $meta->updateWPPost();
		if ( is_wp_error($updateResult)) {
			echo '<li>***Unable to fill content ' . $meta->getPostTitle() . ' from ' . $meta->getSourcePath() . ': '.$updateResult->get_error_message().'</li>';

		} else {
			echo '<li>Content filled from ' . $meta->getSourcePath() . ' into post #' . $updateResult . ' with title ' . $meta->getPostTitle() . '</li>';
		}

	}
} 