<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:28 PM
 */

namespace html_import;

use html_import\indices\WebPage;

require_once( dirname( __FILE__ ) . '/Importer.php' );
require_once( dirname( __FILE__ ) . '/indices/WebPage.php' );

/**
 * Class HTMLStubImporter
 * Importer that does a basic stub import only (creates the page, and sets the title).
 * @package html_import
 */
class HTMLStubImporter extends Importer {

	/**
	 * Executes the stub import by updating the Wordpress post and then adds its references to $html_post_lookup.
	 *
	 * @param WebPage       $webPage
	 * @param WPMetaConfigs $meta
	 * @param null          $html_post_lookup
	 * @param null          $media_lookup
	 *
	 * @return mixed|void
	 */
	protected function doImport( WebPage $webPage, WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null ) {
		$updateResult                              = $meta->updateWPPost();
		$html_post_lookup[$webPage->getFullPath()] = $meta->getPostId();

		if ( is_wp_error( $updateResult ) ) {
			echo '<li>***Unable to create content ' . $meta->getPostTitle() . ' from ' . $webPage->getFullPath() . '</li>';
		} else {
			$webPage->setWPID( $updateResult );
			echo '<li>Stub post created from ' . $webPage->getFullPath() . ' into post #' . $updateResult . ' with title ' . $meta->getPostTitle() . '</li>';
		}

	}
} 