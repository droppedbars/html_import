<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:29 PM
 */

namespace html_import;

use html_import\indices\WebPage;


require_once( dirname( __FILE__ ) . '/Importer.php' );
require_once( dirname( __FILE__ ) . '/indices/WebPage.php' );

/**
 * Class HTMLFullImporter
 * Importer that will import all aspects of an HTML file.  It will update the media, links, and set the template.
 * @package html_import
 */
class HTMLFullImporter extends Importer {
	private $htmlImportStage = null;
	private $mediaImportStage = null;
	private $updateLinksImportStage = null;
	private $GDNHeaderFooterStage = null;

	/**
	 * Initiates all of the import stages that can be executed.
	 * $stages is responsible to define which stages actually will be actioned upon.
	 *
	 * @param admin\HtmlImportSettings $settings
	 * @param HTMLImportStages         $stages
	 */
	function __construct( admin\HtmlImportSettings $settings, HTMLImportStages $stages ) {
		parent::__construct( $settings, $stages );
		$this->htmlImportStage        = new ImportHTMLStage();
		$this->mediaImportStage       = new MediaImportStage();
		$this->updateLinksImportStage = new UpdateLinksImportStage();
		$this->GDNHeaderFooterStage   = new GridDeveloperHeaderFooterImportStage();
		$this->setTemplateStage       = new SetTemplateStage();
	}

	/**
	 * Processes through each stage and if permitted, executes them.
	 *
	 * @param WebPage       $webPage
	 * @param WPMetaConfigs $meta
	 * @param null          $html_post_lookup
	 * @param null          $media_lookup
	 *
	 * @return mixed|void
	 */
	protected function doImport( WebPage $webPage, WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null ) {
		$meta->setPostContent( $meta->getPostContent() );

		$nothing = null;

		$this->stageParse( $webPage, $this->htmlImportStage, $meta, $nothing );
		$this->stageParse( $webPage, $this->GDNHeaderFooterStage, $meta, $nothing );
		$this->stageParse( $webPage, $this->updateLinksImportStage, $meta, $html_post_lookup );
		$this->stageParse( $webPage, $this->mediaImportStage, $meta, $media_lookup );

		$meta->updateWPPost();  // this happens automatically at the end, but needs to happen here to guarantee an ID for the template update

		$postContent = $meta->getPostContent();
		$this->stageParse( $webPage, $this->setTemplateStage, $meta, $postContent, $nothing );

		$updateResult = $meta->updateWPPost();
		if ( is_wp_error( $updateResult ) ) {
			echo '<li>***Unable to fill content ' . $meta->getPostTitle() . ' from ' . $meta->getSourcePath() . ': ' . $updateResult->get_error_message() . '</li>';

		} else {
			echo '<li>Content filled from ' . $meta->getSourcePath() . ' into post #' . $updateResult . ' with title ' . $meta->getPostTitle() . '</li>';
		}

	}
} 