<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-17
 * Time: 8:34 PM
 */

namespace html_import\admin;

require_once( dirname( __FILE__ ) . '/PluginSettingInterface.php' );

class StringSetting implements PluginSettingInterface {
	private $name = null;
	private $value = null;

	/**
	 * @param string $settingName
	 * @param null   $settingValue
	 */
	function __construct($settingName, $settingValue = null)
	{
		$this->name = $settingName;
		$this->value = $settingValue;
	}

	/**
	 * Escapes the value of the setting before providing, makes it safe to enter into HTML element attributes.
	 * @return mixed
	 */
	public function getEscapedAttributeValue() {
		return esc_attr($this->getValue());
	}

	/**
	 * Escapes the value of the setting before providing, makes it safe to display in webpages.
	 * @return mixed
	 */
	public function getEscapedHTMLValue() {
		return esc_html($this->getValue());
	}

	/**
	 * Returns the setting's WordPress name.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the settings value.
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Set a new value to the setting object.
	 *
	 * @param mixed $value new value to assign to the setting
	 *
	 * @return void
	 */
	public function setSettingValue( $value ) {
		$this->value = $value;
	}
}