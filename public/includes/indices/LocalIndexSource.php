<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-07-02
 * Time: 9:31 PM
 */

namespace html_import\indices;

require_once( dirname( __FILE__ ) . '/IndexSource.php' );

class LocalIndexSource extends IndexSource {

	public function getContents() {
		$indexPath = $this->getIndexPath();
		if (file_exists($indexPath)) {
			return file_get_contents($indexPath);
		} else {
			return false;
		}
	}

} 