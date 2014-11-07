<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:11 PM
 */

namespace html_import;

use html_import\indices\WebPage;

require_once( dirname( __FILE__ ) . '/ImportStage.php' );
require_once( dirname( __FILE__ ) . '/HTMLImportStages.php' );
require_once( dirname( __FILE__ ) . '/WPMetaConfigs.php' );
require_once( dirname( __FILE__ ) . '/XMLHelper.php' );
require_once( dirname( __FILE__ ) . '/indices/WebPage.php' );

class ImportHTMLStage extends ImportStage {
	protected function isValid( HTMLImportStages $stagesSettings ) {
		return $stagesSettings->doesImportHtml();
	}

	protected function performStage( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, &$other = null ) {
		$meta->setPostContent( $this->replaceBodyWithDivs( $meta->getPostContent() ) );
	}


	private function replaceBodyWithDivs( $body ) {
		$divBody = XMLHelper::renameTags( $body, 'body', 'div' );

		return $divBody;
	}

} 