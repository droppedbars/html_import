<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-10-06
 * Time: 8:25 PM
 */

namespace html_import\indices;

/**
 * Class WebPageSettings
 * Settings for an individual webpage.
 * @package html_import\indices
 */
class WebPageSettings {
	/* future
private $tags;
private $overwriteExisting;
private $postType;
*/

	private $categoryIds;
	private $overrideSettings;

	/**
	 * Returns the state of the overrideSettings setting.  This defines if the webpage is permitted to use its specific settings over those defined in the plugin interface.
	 * @return bool
	 */
	public function getOverrideSettings() {
		return $this->overrideSettings;
	}

	/**
	 * Sets the value of overrideSettings.
	 *
	 * @param bool $overrideSettings
	 */
	public function setOverrideSettings( $overrideSettings ) {
		if ( !is_bool( $overrideSettings ) ) {
			throw new \InvalidArgumentException( "OverrideSettings must be a boolean value" );
		}
		$this->overrideSettings = $overrideSettings;
	}

	/**
	 * Returns all of the category ids in the settings.
	 * @return Array
	 */
	public function getCategoryIds() {
		return $this->categoryIds;
	}

	/**
	 * Sets all of the category ides.  Must be a valid array of all strings.
	 *
	 * @param Array $categoryIds
	 */
	public function setCategories( Array $categoryIds ) {
		for ( $i = 0; $i < sizeof( $categoryIds ); $i ++ ) {
			if ( !is_string( $categoryIds[$i] ) ) {
				throw new \InvalidArgumentException( 'Array $categoryIds must be all strings.' );
			}
		}
		$this->categoryIds = $categoryIds;
	}

	/**
	 * Add a category ID to the end of the category ID list
	 *
	 * @param $categoryId
	 */
	public function addCategory( $categoryId ) {
		if ( !is_integer( $categoryId ) ) {
			throw new \InvalidArgumentException( "categoryId must be an integer value" );
		}

		$this->categoryIds[] = $categoryId;
	}

	/**
	 * initialize all the settings
	 */
	public function __constructor() {
		$this->categoryIds      = null;
		$this->overrideSettings = false;
	}
} 