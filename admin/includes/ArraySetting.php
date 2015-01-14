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
 * Wrapper class to represent a plugin setting that is intended to be an Array
 * @package html_import\admin
 */
class ArraySetting implements PluginSettingInterface {

	private $name = null;
	private $value = null;
	private $index = 0;

	/**
	 * Initializes the Array class.  If the value is null, then the array will be created with no values.
	 *
	 * @param string $settingName
	 * @param null   $settingValue
	 */
	function __construct( $settingName, $settingValue = null ) {
		$this->name = $settingName;
		if ( is_null( $settingValue ) ) {
			$this->value = Array();
		} else {
			$this->value = $settingValue;
		}
	}

	/**
	 * Sets the current index when looking at the array
	 *
	 * @param int $index
	 *
	 * @throws \InvalidArgumentException If index is not an integer, throws exception
	 * @throws \OutOfRangeException If index is a value that is out of the range of the array.
	 */
	public function setIndex( $index ) {
		if ( !is_integer( $index ) ) {
			throw new \InvalidArgumentException( "Only integer values can be indexes" );
		}
		if ( $index > sizeof( $this->value ) ) {
			throw new \OutOfRangeException( "Index extended beyond the size of the array" );
		}
		$this->index = $index;
	}

	/**
	 * Escapes the value of the setting before providing, makes it safe to enter into HTML element attributes.
	 * @return mixed
	 */
	public function getEscapedAttributeValue() {
		return esc_attr( $this->getValue( $this->index ) );
	}

	/**
	 * Returns the value an in the array.  If $index is null then it will use the current element.  Otherwise it returns the element at the position defined by $index.  If $index is larger than the size of the array, or there is no value there, then it returns null.
	 *
	 * @param int|null $index
	 *
	 * @return mixed
	 * @throws \InvalidArgumentException If index is not an integer, throws exception
	 */
	public function getValue( $index = null ) {
		if ( !is_integer( $index ) ) {
			throw new \InvalidArgumentException( "Only integer values can be indexes" );
		}
		$i = $index;
		if ( is_null( $index ) ) {
			$i = $this->index;
		}
		if ( ( $i >= sizeof( $this->value ) ) || ( is_null( $this->value[$i] ) ) ) {
			// TODO: class should allow testing for size and throw an exception when the index is beyond the size, not return null
			return null;
		} else {
			return $this->value[$i];
		}
	}

	/**
	 * Escapes the value of the setting before providing, makes it safe to display in webpages.
	 * @return mixed
	 */
	public function getEscapedHTMLValue() {
		return esc_html( $this->getValue( $this->index ) );
	}

	/**
	 * Returns the name of the setting
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set the $value into the array at the location defined by $index, or if $index is null, the current position in the array.  The $index can be anything within the array size, or add to the end, but it cannot be an integer extending beyond the size of the array.
	 *
	 * @param mixed    $value
	 * @param int|null $index
	 *
	 * @throws \InvalidArgumentException If index is not an integer, throws exception
	 * @throws \OutOfRangeException If index is a value that is out of the range of the array+1
	 */
	public function setSettingValue( $value, $index = null ) {
		if ( !is_integer( $index ) ) {
			throw new \InvalidArgumentException( "Only integer values can be indexes" );
		}
		if ( $index > sizeof( $this->value ) ) {
			throw new \OutOfRangeException( "Index extended beyond the size of the array" );
		}
		if ( is_null( $index ) ) {
			$this->value[$this->index] = $value;
		} else {
			$this->value[$index] = $value;
		}
	}

	/**
	 * Pushes a value onto the end of the array.
	 *
	 * @param $value
	 */
	public function addValue( $value ) {
		array_push( $this->value, $value );
	}

	/**
	 * Tests to see if $value is contained within the array.  Returns true if it is, false otherwise.
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function testValue( $value ) {
		$test = in_array( $value, $this->value );

		return $test;
	}

	/**
	 * Returns the entire raw array
	 * @return null|Array
	 */
	public function getValuesArray() {
		return $this->value;
	}
}