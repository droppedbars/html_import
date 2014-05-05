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

class SetTemplateStage extends ImportStage {
	protected function isValid(HTMLImportStages $stagesSettings) {
		return $stagesSettings->doesConfigureTemplate();
	}

	protected function performStage(HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, $body, &$other = null) {
		update_post_meta( $meta->getPostId(), '_wp_page_template', $meta->getPageTemplate() );
	}

} 