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
 * Class FolderImporter
 * Importer than imports folders into wordpress as empty pages.
 * @package html_import
 */
class FolderImporter extends Importer {

	/**
	 * Performs the import of the current webPage object as a folder.
	 *
	 * @param WebPage       $webPage
	 * @param WPMetaConfigs $meta
	 * @param Array|null    $html_post_lookup
	 * @param Array|null    $media_lookup
	 *
	 * @return null
	 */
	protected function doImport( WebPage $webPage, WPMetaConfigs $meta, &$html_post_lookup = null, &$media_lookup = null ) {
		$updateResult = $meta->updateWPPost();

		if ( is_wp_error( $updateResult ) ) {
			echo '<li>***Unable to create folder ' . $meta->getPostTitle() . ' from ' . $meta->getSourcePath() . '</li>';
		} else {
			$webPage->setWPID( $updateResult );
			echo '<li>Folder created from ' . $meta->getPostTitle() . ' into post #' . $updateResult . ' with title ' . $meta->getPostTitle() . '</li>';
		}

	}
} 