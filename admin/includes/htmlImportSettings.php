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

class HtmlImportSettings implements PluginSettingsInterface {
	const SETTINGS_NAME = 'htim_importer_options';

	// TODO: radio/checkbox type options should have set enum types available to them
	private $index_type = null;
	private $file_type = null;
	private $import_source = null;
	private $file_location = null;
	private $parent_page = null;
	private $template = null;

	const INDEX_DEFAULT = 'flare';
	const FILE_TYPE_DEFAULT = 'zip';
	const IMPORT_SRC_DEFAULT = 'upload';
	const PARENT_PAGE_DEFAULT = 0;
	const TEMPLATE_DEFAULT = 0;
	const FILE_LOCATION_DEFAULT = '';


	function __construct() {
		$this->index_type = new StringSetting('index-type');
		$this->file_type = new StringSetting('file-type');
		$this->import_source = new StringSetting('import-source');
		$this->file_location = new StringSetting('file-location');
		$this->parent_page = new StringSetting('parent_page');
		$this->template = new StringSetting('template');
	}

	private function loadDefaults() {
		$this->index_type->setSettingValue(self::INDEX_DEFAULT);
		$this->file_type->setSettingValue(self::FILE_TYPE_DEFAULT);
		$this->import_source->setSettingValue(self::IMPORT_SRC_DEFAULT);
		$this->parent_page->setSettingValue(self::PARENT_PAGE_DEFAULT);
		$this->template->setSettingValue(self::TEMPLATE_DEFAULT);
		$this->file_location->setSettingValue(self::FILE_LOCATION_DEFAULT);
	}

	/**
	 * @return bool|void
	 */
	public function loadFromDB() {
		$this->loadDefaults();

		$plugin_options_arr = get_site_option(self::SETTINGS_NAME);

		if (isset($plugin_options_arr['index-type'])) {
			$index_type = $plugin_options_arr['index-type'];
			$this->index_type->setSettingValue($index_type);
		}
		if (isset($plugin_options_arr['file-type'])) {
			$file_type = $plugin_options_arr['file-type'];
			$this->file_type->setSettingValue($file_type);
		}
		if (isset($plugin_options_arr['import-source'])) {
			$import_source = $plugin_options_arr['import-source'];
			$this->import_source->setSettingValue($import_source);
		}
		if (isset($plugin_options_arr['parent_page'])) {
			$parent_page = $plugin_options_arr['parent_page'];
			$this->parent_page->setSettingValue($parent_page);
		}
		if (isset($plugin_options_arr['template'])) {
			$template = $plugin_options_arr['template'];
			$this->template->setSettingValue($template);
		}
		if (isset($plugin_options_arr['file-location'])) {
			$file_location = $plugin_options_arr['file-location'];
			$this->file_location->setSettingValue($file_location);
		}
	}

	/**
	 * @return bool|void
	 */
	public function saveToDB() {
		return update_site_option(self::SETTINGS_NAME,
				Array($this->index_type->getName() 		=> $this->index_type->getValue(),
							$this->file_type->getName() 		=> $this->file_type->getValue(),
							$this->import_source->getName() => $this->import_source->getValue(),
							$this->file_location->getName() => $this->file_location->getValue(),
							$this->parent_page->getName() 	=> $this->parent_page->getValue(),
							$this->template->getName() 			=> $this->template->getValue()));
	}

	/**
	 *
	 */
	public function loadFromPOST() {
		$this->loadDefaults();

		if (isset($_POST[$this->file_location->getName()])) {
			$file_location = $_POST[$this->file_location->getName()];
			$this->file_location->setSettingValue($file_location);
		}

		if (isset($_POST[$this->index_type->getName()])) {
			if (strcmp($_POST[$this->index_type->getName()], 'xml') == 0) {
				$index_type = 'xml';
			} else {
				$index_type = 'flare';
			}
			$this->index_type->setSettingValue($index_type);
		}
		if (isset($_POST[$this->file_type->getName()])) {
			if (strcmp($_POST[$this->file_type->getName()], 'index') == 0) {
				$file_type = 'index';
			} else {
				$file_type = 'zip';
			}
			$this->file_type->setSettingValue($file_type);
		}
		if (isset($_POST[$this->import_source->getName()])) {
			if (strcmp($_POST[$this->import_source->getName()], 'location') == 0) {
				$import_source = 'location';
			} else {
				$import_source = 'upload';
			}
			$this->import_source->setSettingValue($import_source);
		}

		if (isset($_POST[$this->parent_page->getName()])) {
			// TODO: returns 0 if it fails?  Better ways to do this and handle errors
			$parent_page = intval(sanitize_text_field($_POST[$this->parent_page->getName()]));
			$this->parent_page->setSettingValue($parent_page);
		}
		if (isset($_POST[$this->template->getName()])) {
			$template = sanitize_text_field($_POST[$this->template->getName()]);
			$this->template->setSettingValue($template);
		}
	}

	/**
	 * Removes the settings from the WordPress database
	 * @return bool True if successfully delete, false otherwise
	 */
	public function deleteFromDB() {
		return delete_site_option(self::SETTINGS_NAME);
	}

	public function getIndexType() {
		return $this->index_type;
	}
	public function getFileType() {
		return $this->file_type;
	}
	public function getImportSource() {
		return $this->import_source;
	}
	public function getParentPage() {
		return $this->parent_page;
	}
	public function getTemplate() {
		return $this->template;
	}
	public function getFileLocation() {
		return $this->file_location;
	}

} 