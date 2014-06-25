<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-06-12
 * Time: 8:15 PM
 */
require_once( dirname( __FILE__ ) . '/../public/includes/XMLHelper.php' );


function replaceBodyWithDivs($body) {
	// TODO

	$divBody = html_import\XMLHelper::renameTags($body, 'body', 'div');


	return $divBody;
}


$body = '<body value="foo">some text<h1>some more text</h1>Oh the <b>insanity</b></body>';

$divs = replaceBodyWithDivs($body);

echo "\n\n".$body."\n\n";
echo $divs;