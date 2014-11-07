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

abstract class ImportStage {
	abstract protected function isValid( HTMLImportStages $stagesSettings );

	abstract protected function performStage( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, &$other = null );

	final public function process( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs $meta, &$other = null ) {
		if ( $this->isValid( $stagesSettings ) ) {
			$this->performStage( $webPage, $stagesSettings, $meta, $other );
		}
	}
} 