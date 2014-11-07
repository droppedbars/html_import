<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:13 PM
 */

namespace html_import;

use html_import\indices\WebPage;


require_once( dirname( __FILE__ ) . '/ImportStage.php' );
require_once( dirname( __FILE__ ) . '/HTMLImportStages.php' );
require_once( dirname( __FILE__ ) . '/WPMetaConfigs.php' );
require_once( dirname( __FILE__ ) . '/indices/WebPage.php' );

class GridDeveloperHeaderFooterImportStage extends ImportStage {
	protected function isValid( HTMLImportStages $stagesSettings ) {
		return $stagesSettings->doesAddGDNHeaderAndFooter();
	}

	protected function performStage( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, &$other = null ) {
		$meta->setPostContent( $this->getGridDirectorHeader( $meta->getPostTitle() ) . $meta->getPostContent() . $this->getGridDirectorFooter() );
	}

	private function getGridDirectorHeader( $title ) {
		$title  = 'Grid Director Developer Network : ' . $title;
		$header = '<h2 class="resource-title">' . $title . '</h2>';

		return $header;
	}

	private function getGridDirectorFooter() {
		return '';
	}

} 