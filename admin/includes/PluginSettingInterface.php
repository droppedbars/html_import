<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-17
 * Time: 8:20 PM
 */

namespace html_import\admin;

/**
 * Interface PluginSettingInterface
 * Defines the required interfaces to be a setting for a Wordpress plugin.  Requires a matching of name and value and the ability to sanitize values before returning them.
 * @package html_import\admin
 */
interface PluginSettingInterface {
	/**
	 * Creates the setting object and set the $settingName and optionally set the $settingValue
	 *
	 * @param string $settingName  name of the setting used for the WordPress database
	 * @param mixed  $settingValue value of the setting.  NULL if not provided.
	 */
	function __construct( $settingName, $settingValue = null );

	/**
	 * Returns the setting's WordPress name.
	 * @return string
	 */
	public function getName();

	/**
	 * Returns the settings value.
	 * @return mixed
	 */
	public function getValue();

	/**
	 * Set a new value to the setting object.
	 *
	 * @param mixed $value new value to assign to the setting
	 *
	 * @return void
	 */
	public function setSettingValue( $value );

	/**
	 * Escapes the value of the setting before providing, makes it safe to enter into HTML element attributes.
	 * @return mixed
	 */
	public function getEscapedAttributeValue();

	/**
	 * Escapes the value of the setting before providing, makes it safe to display in webpages.
	 * @return mixed
	 */
	public function getEscapedHTMLValue();

}