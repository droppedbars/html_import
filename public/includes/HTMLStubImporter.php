<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:28 PM
 */

namespace html_import;

require_once( dirname( __FILE__ ) . '/Importer.php' );

class HTMLStubImporter extends Importer{

	protected function doImport(WPMetaConfigs $meta, $body, &$html_post_lookup = null, &$media_lookup = null) {
		$updateResult = $meta->updateWPPost();
		$html_post_lookup[$meta->getSourcePath()] = $meta->getPostId();

		if ( is_wp_error( $updateResult ) ) {
			echo '<li>***Unable to create content ' . $meta->getPostTitle() . ' from ' . $meta->getSourcePath() . '</li>';
		} else {
			echo '<li>Stub post created from ' . $meta->getSourcePath() . ' into post #' . $updateResult . ' with title ' . $meta->getPostTitle() . '</li>';
		}

	}
} 