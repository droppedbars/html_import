<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-17
 * Time: 8:34 PM
 */

namespace html_import\admin;

require_once( dirname( __FILE__ ) . '/PluginSettingInterface.php' );

/**
 * Class StringSetting
 * Specialized Wordpress setting for string values.
 * @package html_import\admin
 */
class StringSetting implements PluginSettingInterface {
	private $name = null;
	private $value = null;

	/**
	 * Set the name of the setting and the value if applicable.  $settingValue must be null, a string or a number.  It cannot be an array, object or anything else.
	 *
	 * @param string      $settingName
	 * @param string|null $settingValue
	 *
	 * @throws \InvalidArgumentException
	 */
	function __construct( $settingName, $settingValue = null ) {
		if ( !$this->is_string( $settingValue ) ) {
			throw new \InvalidArgumentException( '$settingValue must be a string, or number' );
		}
		$this->name  = $settingName;
		$this->value = $settingValue . ''; // ensures it's cast to a string
	}

	/**
	 * Returns true if the passed in value is either null, a string, or number.
	 *
	 * @param $settingValue
	 *
	 * @return bool
	 */
	private function is_string( $settingValue ) {
		return is_null( $settingValue ) || is_string( $settingValue ) || is_integer( $settingValue ) || is_float( $settingValue );
	}

	/**
	 * Escapes the value of the setting before providing, makes it safe to enter into HTML element attributes.
	 * @return mixed
	 */
	public function getEscapedAttributeValue() {
		return esc_attr( $this->getValue() );
	}

	/**
	 * Returns the settings value.
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Escapes the value of the setting before providing, makes it safe to display in webpages.
	 * @return mixed
	 */
	public function getEscapedHTMLValue() {
		return esc_html( $this->getValue() );
	}

	/**
	 * Returns the setting's WordPress name.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set a new value to the setting object.  The value must be null, a string, or a number.
	 *
	 * @param mixed $value new value to assign to the setting
	 *
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function setSettingValue( $value ) {
		if ( !$this->is_string( $value ) ) {
			throw new \InvalidArgumentException( '$settingValue must be a string, or number' );
		}
		$this->value = $value;
	}
}