<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-10-06
 * Time: 8:25 PM
 */

namespace html_import\indices;


class WebPageSettings {
	/* future
private $tags;
private $overwriteExisting;
private $postType;
*/

	private $categoryIds;
	private $overrideSettings;

	/**
	 * @return bool
	 */
	public function getOverrideSettings() {
		return $this->overrideSettings;
	}

	/**
	 * @param bool $overrideSettings
	 */
	public function setOverrideSettings( $overrideSettings ) {
		$this->overrideSettings = $overrideSettings;
	}

	/**
	 * @return Array
	 */
	public function getCategoryIds() {
		return $this->categoryIds;
	}

	/**
	 * @param Array $categoryIds
	 */
	public function setCategories( Array $categoryIds ) {
		$this->categoryIds = $categoryIds;
	}

	public function addCategory( $categoryId ) {
		$this->categoryIds[] = $categoryId ;
	}

	public function __constructor() {
		$this->categoryIds = null;
		$this->overrideSettings = false;
	}
} 