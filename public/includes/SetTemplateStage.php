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
require_once( dirname( __FILE__ ) . '/indices/WebPage.php' );

/**
 * Class SetTemplateStage
 * Stage that is responsible for ensuring an imported page has the correct page template assigned to it.
 * @package html_import
 */
class SetTemplateStage extends ImportStage {
	protected function isValid( HTMLImportStages $stagesSettings ) {
		return $stagesSettings->doesConfigureTemplate();
	}

	/**
	 * Performs the action of setting the appropriate template to the page.
	 *
	 * @param WebPage          $webPage
	 * @param HTMLImportStages $stagesSettings
	 * @param WPMetaConfigs    $meta
	 * @param null             $other
	 *
	 * @return null
	 */
	protected function performStage( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, &$other = null ) {
		update_post_meta( $meta->getPostId(), '_wp_page_template', $meta->getPageTemplate() );
	}

} 