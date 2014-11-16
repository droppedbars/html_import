<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-17
 * Time: 8:13 PM
 */

namespace html_import\admin;

/**
 * Interface PluginSettingsInterface
 * Defines the Interface required for all settings in a WordPress plugin.
 * @package html_import\admin
 */
interface PluginSettingsInterface {

	/**
	 * Load the settings from the WordPress database.
	 * @return bool returns true if the settings were found in the database, false otherwise.
	 */
	public function loadFromDB();

	/**
	 * Save the settings from the WordPress database.
	 * @return bool returns true if successful, false otherwise.
	 */

	public function saveToDB();

	/**
	 * Reads the settings in from the POST object and stores them.
	 * @return void
	 */
	public function loadFromPOST();

	/**
	 * Removes the settings from the WordPress database
	 * @return bool True if successfully delete, false otherwise
	 */
	public function deleteFromDB();

} 