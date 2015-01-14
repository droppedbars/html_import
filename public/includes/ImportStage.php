<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:10 PM
 */

namespace html_import;

use html_import\indices\WebPage;

require_once( dirname( __FILE__ ) . '/indices/WebPage.php' );

/**
 * Class ImportStage
 * Handles individual aspects of importing a webpage in single stages.
 * @package html_import
 */
abstract class ImportStage {
	/**
	 * Public function that is called to get the import stage to be activated.
	 *
	 * @param WebPage          $webPage
	 * @param HTMLImportStages $stagesSettings
	 * @param WPMetaConfigs    $meta
	 * @param null             $other
	 */
	final public function process( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs $meta, &$other = null ) {
		if ( $this->isValid( $stagesSettings ) ) {
			$this->performStage( $webPage, $stagesSettings, $meta, $other );
		}
	}

	abstract protected function isValid( HTMLImportStages $stagesSettings );

	/**
	 * Function responsible for actually performing the action of the import class.
	 *
	 * @param WebPage          $webPage
	 * @param HTMLImportStages $stagesSettings
	 * @param WPMetaConfigs    $meta
	 * @param null             $other
	 *
	 * @return mixed
	 */
	abstract protected function performStage( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, &$other = null );
} 