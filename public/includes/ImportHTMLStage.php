<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:11 PM
 */

namespace html_import;


class ImportHTMLStage extends ImportStage {
	protected function isValid(HTMLImportStages $stagesSettings) {
		return $stagesSettings->doesImportHtml();
	}

	protected function performStage(HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, $body, &$other = null) {
		$meta->setPostContent($this->replaceBodyWithDivs($body));
	}


	private function replaceBodyWithDivs($body) {
		// TODO
		return $body;
	}

} 