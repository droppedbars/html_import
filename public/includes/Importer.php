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

/**
 * Class Importer
 * Base class of family than deals with importing different things into Wordpress.
 * @package html_import
 */
abstract class Importer {

	protected $settings = null;
	protected $stages = null;

	/**
	 * Initialize the object with settings for the import and the stages to be imported.
	 *
	 * @param admin\HtmlImportSettings $settings
	 * @param HTMLImportStages         $stages
	 */
	public function __construct( admin\HtmlImportSettings $settings, HTMLImportStages $stages ) {
		$this->settings = $settings;
		$this->stages   = $stages;
	}

	/**
	 * Used to initiate the import of the webpage given configuration details.
	 *
	 * @param WebPage       $webPage
	 * @param WPMetaConfigs $meta
	 * @param Array|null    $html_post_lookup array of pages that have been imported
	 * @param Array|null    $media_lookup     array of media files that have been imported
	 */
	public function import( WebPage $webPage, WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null ) {
		$this->doImport( $webPage, $meta, $html_post_lookup, $media_lookup );
		$this->save( $meta );
	}

	/**
	 * Performs the actual import of the webpage.
	 *
	 * @param WebPage       $webPage
	 * @param WPMetaConfigs $meta
	 * @param Array|null    $html_post_lookup
	 * @param Array|null    $media_lookup
	 *
	 * @return mixed
	 */
	abstract protected function doImport( WebPage $webPage, WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null );

	/**
	 * Saves the post in wordpress.
	 *
	 * @param WPMetaConfigs $meta
	 *
	 * @return int|\WP_Error
	 */
	protected function save( WPMetaConfigs $meta ) {
		return $meta->updateWPPost();
	}

	/**
	 * Responsible for executing each individual stage as they are passed in.
	 *
	 * @param WebPage       $webPage
	 * @param ImportStage   $stage
	 * @param WPMetaConfigs $meta
	 * @param               $other
	 */
	protected function stageParse( WebPage $webPage, ImportStage $stage, WPMetaConfigs $meta, &$other ) {
		$stage->process( $webPage, $this->stages, $meta, $other );
	}
} 