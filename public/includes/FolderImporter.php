<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:28 PM
 */

namespace html_import;

require_once( dirname( __FILE__ ) . '/Importer.php' );

class FolderImporter extends Importer{


	function __construct() {
	}
	protected function doImport(admin\HtmlImportSettings $settings, HTMLImportStages $stages, WPMetaConfigs $meta, $body, &$html_post_lookup = null, &$media_lookup = null) {
		$updateResult = $meta->updateWPPost();

		if ( is_wp_error( $updateResult ) ) {
			echo '<li>***Unable to folder ' . $meta->getPostTitle() . ' from ' . $meta->getSourcePath() . '</li>';
		} else {
			echo '<li>Folder created from ' . $meta->getPostTitle() . ' into post #' . $updateResult . ' with title ' . $meta->getPostTitle() . '</li>';
		}

	}
} 