<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-03-18
 * Time: 8:07 PM
 */

function testfunction( &$testArray ) {
	echo 'isset ' . isset( $testArray ) . ' ';
	print_r( $testArray );

	return $testArray;
}

$testArray = Array();

echo 'isset ' . isset( $testArray ) . ' ';
print_r( $testArray );
$testArray = testfunction( $testArray );
echo 'isset ' . isset( $testArray ) . ' ';
print_r( $testArray );

$testArray['foo'] = '1234';
echo 'isset ' . isset( $testArray ) . ' ';
print_r( $testArray );
$testArray = testfunction( $testArray );
echo 'isset ' . isset( $testArray ) . ' ';
print_r( $testArray );
