<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-17
 * Time: 8:16 PM
 */

namespace html_import\admin;

require_once( dirname( __FILE__ ) . '/PluginSettingsInterface.php' );
require_once( dirname( __FILE__ ) . '/StringSetting.php' );
require_once( dirname( __FILE__ ) . '/ArraySetting.php' );

/**
 * Class HtmlImportSettings
 * Represents all settings used by for the HTML importer.  Handles default values, prepping the database, saving, and loading from POST/
 * @package html_import\admin
 */
class HtmlImportSettings implements PluginSettingsInterface {
	const SETTINGS_NAME = 'htim_importer_options';

	// TODO: radio/checkbox type options should have set enum types available to them
	const INDEX_DEFAULT = 'flare';
	const FILE_TYPE_DEFAULT = 'zip';
	const IMPORT_SRC_DEFAULT = 'upload';
	const PARENT_PAGE_DEFAULT = 0;
	const TEMPLATE_DEFAULT = 0;
	const FILE_LOCATION_DEFAULT = '';
	const OVERWRITE_FILES_DEFAULT = 'true';
	private $index_type = null;
	private $file_type = null;
	private $import_source = null;
	private $file_location = null;
	private $parent_page = null;
	private $template = null;
	private $category = null;
	private $doesOverwriteFiles = null;

	/**
	 * Instantiates all of the settings available for the plugin
	 */
	function __construct() {
		$this->index_type         = new StringSetting( 'index-type' );
		$this->file_type          = new StringSetting( 'file-type' );
		$this->import_source      = new StringSetting( 'import-source' );
		$this->file_location      = new StringSetting( 'file-location' );
		$this->parent_page        = new StringSetting( 'parent_page' );
		$this->template           = new StringSetting( 'template' );
		$this->category           = new ArraySetting( 'category' );
		$this->doesOverwriteFiles = new StringSetting( 'overwrite-files' ); // TODO: should make a BoolSetting
		$this->loadDefaults();
	}

	/**
	 * Loads all of the plugin settings with values stored in the Wordpress database
	 * @return bool|void
	 */
	public function loadFromDB() {
		$this->loadDefaults();

		$plugin_options_arr = get_site_option( self::SETTINGS_NAME );

		if ( isset( $plugin_options_arr['index-type'] ) ) {
			$index_type = $plugin_options_arr['index-type'];
			$this->index_type->setSettingValue( $index_type );
		}
		if ( isset( $plugin_options_arr['file-type'] ) ) {
			$file_type = $plugin_options_arr['file-type'];
			$this->file_type->setSettingValue( $file_type );
		}
		if ( isset( $plugin_options_arr['import-source'] ) ) {
			$import_source = $plugin_options_arr['import-source'];
			$this->import_source->setSettingValue( $import_source );
		}
		if ( isset( $plugin_options_arr['parent_page'] ) ) {
			$parent_page = $plugin_options_arr['parent_page'];
			$this->parent_page->setSettingValue( $parent_page );
		}
		if ( isset( $plugin_options_arr['template'] ) ) {
			$template = $plugin_options_arr['template'];
			$this->template->setSettingValue( $template );
		}
		if ( isset( $plugin_options_arr['file-location'] ) ) {
			$file_location = $plugin_options_arr['file-location'];
			$this->file_location->setSettingValue( $file_location );
		}
		if ( isset( $plugin_options_arr['overwrite-files'] ) ) {
			$overwrite_files = $plugin_options_arr['overwrite-files'];
			$this->doesOverwriteFiles->setSettingValue( $overwrite_files );
		}
		$counter        = 0;
		$this->category = new ArraySetting( 'category' );
		do {
			if ( isset( $plugin_options_arr[$this->category->getName() . '_' . $counter] ) ) {
				$this->category->addValue( $plugin_options_arr[$this->category->getName() . '_' . $counter] );
				$counter ++;
			} else {
				break;
			}
		} while ( 1 == 1 );
	}

	/**
	 * Populate all settings with default values.
	 */
	private function loadDefaults() {
		$this->index_type->setSettingValue( self::INDEX_DEFAULT );
		$this->file_type->setSettingValue( self::FILE_TYPE_DEFAULT );
		$this->import_source->setSettingValue( self::IMPORT_SRC_DEFAULT );
		$this->parent_page->setSettingValue( self::PARENT_PAGE_DEFAULT );
		$this->template->setSettingValue( self::TEMPLATE_DEFAULT );
		$this->file_location->setSettingValue( self::FILE_LOCATION_DEFAULT );
		$this->category->setSettingValue( 0, 0 ); // TODO: 0 as default "none", could be better
		$this->doesOverwriteFiles->setSettingValue( self::OVERWRITE_FILES_DEFAULT );
	}

	/**
	 * Saves all of the settings to the Wordpress database.
	 * @return bool|void
	 */
	public function saveToDB() {
		$settings = Array( $this->index_type->getName()         => $this->index_type->getValue(),
											 $this->file_type->getName()          => $this->file_type->getValue(),
											 $this->import_source->getName()      => $this->import_source->getValue(),
											 $this->file_location->getName()      => $this->file_location->getValue(),
											 $this->parent_page->getName()        => $this->parent_page->getValue(),
											 $this->template->getName()           => $this->template->getValue(),
											 $this->doesOverwriteFiles->getName() => $this->doesOverwriteFiles->getValue() );
		$counter  = 0;
		do {
			if ( !is_null( $this->category->getValue( $counter ) ) ) {
				$settings[$this->category->getName() . '_' . $counter] = $this->category->getValue( $counter );
				$counter ++;
			} else {
				break;
			}
		} while ( 1 == 1 );

		return update_site_option( self::SETTINGS_NAME, $settings );

	}

	/**
	 * Validates that required settings are correctly set.
	 * It does a simple true/false and does not identify the offending setting.
	 */
	public function validate() {
		if ( isset( $_POST[$this->index_type->getName()] ) ) {
			if ( ( strcmp( $_POST[$this->index_type->getName()], 'xml' ) != 0 ) && ( strcmp( $_POST[$this->index_type->getName()], 'flare' ) != 0 ) /*&& ( strcmp( $_POST[$this->index_type->getName()], 'crawl' ) != 0 )*/ ) {
				echo 'Invalid index type.<br>';

				return false;
			}
		} else {
			echo 'Missing index type.<br>';

			return false;
		}

		if ( isset( $_POST[$this->import_source->getName()] ) ) {
			if ( strcmp( $_POST[$this->import_source->getName()], 'location' ) == 0 ) {
				if ( ( !isset( $_POST[$this->file_location->getName()] ) ) || ( strlen( $_POST[$this->file_location->getName()] ) <= 0 ) ) {
					echo 'Missing file location.<br>';

					return false;
				}
			} else {
				if ( strcmp( $_POST[$this->import_source->getName()], 'upload' ) == 0 ) {
					if ( ( !isset( $_FILES['file-upload'] ) ) || ( $_FILES['file-upload']['error'] != 0 ) ) {
						echo 'Error with file to upload.<br>';

						return false;
					}
				} else {
					echo 'Invalid import source.<br>';

					return false;
				}
			}
		} else {
			echo 'Missing import source.<br>';

			return false;
		}

		if ( isset( $_POST[$this->file_type->getName()] ) ) {
			if ( ( strcmp( $_POST[$this->file_type->getName()], 'index' ) != 0 ) && ( strcmp( $_POST[$this->file_type->getName()], 'zip' ) ) ) {
				echo 'Invalid file type.<br>';

				return false;
			}
		} else {
			echo 'Missing file type.<br>';

			return false;
		}

		return true;
	}

	/**
	 * Loads all of the plugin settings based on content in the POST.
	 */
	public function loadFromPOST() {
		$this->loadDefaults();

		if ( isset( $_POST[$this->file_location->getName()] ) ) {
			$file_location = $_POST[$this->file_location->getName()];
			$this->file_location->setSettingValue( $file_location );
		}

		if ( isset( $_POST[$this->index_type->getName()] ) ) {
			if ( strcmp( $_POST[$this->index_type->getName()], 'xml' ) == 0 ) {
				$index_type = 'xml';
			} else {
				if ( strcmp( $_POST[$this->index_type->getName()], 'flare' ) == 0 ) {
					$index_type = 'flare';
				} else {
					$index_type = 'crawl';
				}
			}
			$this->index_type->setSettingValue( $index_type );
		}
		if ( isset( $_POST[$this->file_type->getName()] ) ) {
			if ( strcmp( $_POST[$this->file_type->getName()], 'index' ) == 0 ) {
				$file_type = 'index';
			} else {
				$file_type = 'zip';
			}
			$this->file_type->setSettingValue( $file_type );
		}
		if ( isset( $_POST[$this->import_source->getName()] ) ) {
			if ( strcmp( $_POST[$this->import_source->getName()], 'location' ) == 0 ) {
				$import_source = 'location';
			} else {
				$import_source = 'upload';
			}
			$this->import_source->setSettingValue( $import_source );
		}

		if ( isset( $_POST[$this->parent_page->getName()] ) ) {
			// TODO: returns 0 if it fails?  Better ways to do this and handle errors
			$parent_page = intval( sanitize_text_field( $_POST[$this->parent_page->getName()] ) );
			$this->parent_page->setSettingValue( $parent_page );
		}
		if ( isset( $_POST[$this->template->getName()] ) ) {
			$template = sanitize_text_field( $_POST[$this->template->getName()] );
			$this->template->setSettingValue( $template );
		}

		$this->category = new ArraySetting( 'category' );
		$catName        = $this->category->getName();
		if ( isset( $_POST[$catName] ) ) {
			$catArray = $_POST[$catName];
			foreach ( $catArray as $cat ) {
				$sanitized_cat = sanitize_text_field( $cat );
				$this->category->addValue( $sanitized_cat );
			}
		}

		if ( isset( $_POST[$this->doesOverwriteFiles->getName()] ) ) {
			if ( strcmp( $_POST[$this->doesOverwriteFiles->getName()], 'false' ) == 0 ) {
				$overwrite_source = 'false';
			} else {
				$overwrite_source = 'true';
			}
			$this->doesOverwriteFiles->setSettingValue( $overwrite_source );
		}

	}

	/**
	 * Removes the settings from the WordPress database
	 * @return bool True if successfully delete, false otherwise
	 */
	public function deleteFromDB() {
		return delete_site_option( self::SETTINGS_NAME );
	}

	/**
	 * Returns the setting: IndexType
	 * @return StringSetting|null
	 */
	public function getIndexType() {
		return $this->index_type;
	}

	/**
	 * Return the setting: FileType
	 * @return StringSetting|null
	 */
	public function getFileType() {
		return $this->file_type;
	}

	/**
	 * Return the setting: ImportSource
	 * @return StringSetting|null
	 */
	public function getImportSource() {
		return $this->import_source;
	}

	/**
	 * Return the setting: ParentPage
	 * @return StringSetting|null
	 */
	public function getParentPage() {
		return $this->parent_page;
	}

	/**
	 * Return the setting: Template
	 * @return StringSetting|null
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * Return the setting: FileLocation
	 * @return StringSetting|null
	 */
	public function getFileLocation() {
		return $this->file_location;
	}

	/**
	 * Return the setting: Categories
	 * @return ArraySetting|null
	 */
	public function getCategories() {
		return $this->category;
	}

	/**
	 * Return the setting: doesOverwriteFiles
	 * @return StringSetting|null
	 */
	public function doesOverwriteFiles() {
		return $this->doesOverwriteFiles;
	}

} 
