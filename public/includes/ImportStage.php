<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:10 PM
 */

namespace html_import;


abstract class ImportStage {
	abstract protected function isValid(HTMLImportStages $stagesSettings);

	abstract protected function performStage(HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, $body, &$other = null);

	final public function process(HTMLImportStages $stagesSettings, WPMetaConfigs $meta, $body, &$other = null) {
		if ($this->isValid($stagesSettings)) {
			$this->performStage($stagesSettings, $meta, $body, $other);
		}
	}
} 