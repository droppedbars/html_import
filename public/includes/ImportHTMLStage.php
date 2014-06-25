<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:11 PM
 */

namespace html_import;

require_once( dirname( __FILE__ ) . '/ImportStage.php' );
require_once( dirname( __FILE__ ) . '/HTMLImportStages.php' );
require_once( dirname( __FILE__ ) . '/WPMetaConfigs.php' );
require_once( dirname( __FILE__ ) . '/XMLHelper.php' );

class ImportHTMLStage extends ImportStage {
	protected function isValid(HTMLImportStages $stagesSettings) {
		return $stagesSettings->doesImportHtml();
	}

	protected function performStage(HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, $body, &$other = null) {
		$meta->setPostContent($this->replaceBodyWithDivs($body));
	}


	private function replaceBodyWithDivs($body) {
		$divBody = XMLHelper::renameTags($body, 'body', 'div');

		return $divBody;
	}

} 