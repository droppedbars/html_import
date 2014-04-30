<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-17
 * Time: 8:34 PM
 */

namespace html_import\admin;

require_once( dirname( __FILE__ ) . '/PluginSettingInterface.php' );

// TODO: this class fails the sniff test.  Needless complex and somewhat hacky
/**
 * Class ArraySetting
 * @package html_import\admin
 */
class ArraySetting implements PluginSettingInterface {
	/**
	 * @var null|string
	 */
	private $name = null;
	/**
	 * @var null
	 */
	private $value = null;
	/**
	 * @var int
	 */
	private $index = 0;

	/**
	 * @param string $settingName
	 * @param null   $settingValue
	 */
	function __construct($settingName, $settingValue = null)
	{
		$this->name = $settingName;
		if (is_null($settingValue)) {
			$this->value = Array();
		} else {
			$this->value = $settingValue;
		}
	}

	/**
	 * @param $index
	 */
	public function setIndex($index) {
		$this->index = $index;
	}

	/**
	 * Escapes the value of the setting before providing, makes it safe to enter into HTML element attributes.
	 * @return mixed
	 */
	public function getEscapedAttributeValue() {
		return esc_attr($this->getValue($this->index));
	}

	/**
	 * Escapes the value of the setting before providing, makes it safe to display in webpages.
	 * @return mixed
	 */
	public function getEscapedHTMLValue() {
		return esc_html($this->getValue($this->index));
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param null $index
	 *
	 * @return mixed
	 */
	public function getValue($index = null) {
		$i = $index;
		if (is_null($index)) {
			$i = $this->index;
		}
		if (is_null($this->value[$i])) {
			return null;
		} else {
			return $this->value[$i];
		}
	}

	/**
	 * @param mixed $value
	 * @param null  $index
	 */
	public function setSettingValue( $value, $index = null ) {
		if (is_null($index)) {
			$this->value[$this->index] = $value;
		} else {
			$this->value[$index] = $value;
		}

	}

	public function addValue( $value ) {
		array_push($this->value, $value);
	}

	public function testValue ($value) {
		$test = in_array($value, $this->value);
		return $test;
	}

	public function getValuesArray() {
		return $this->value;
	}
}