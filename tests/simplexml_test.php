<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-03-16
 * Time: 4:50 PM
 */

function readxml( $xml ) {
	foreach ( $xml->children() as $child ) {
		print_r( $child );
		readxml( $child );
	}
}

function changeBodyToDiv( SimpleXMLElement $htmlFile ) {
	$bodies = $htmlFile->xpath( '/body' );
	if ( $bodies ) {
		foreach ( $bodies as $body ) {
			$content = $body->asXML();
		}
	}
}


$doc                      = new DOMDocument();
$doc->strictErrorChecking = false;
$doc->loadHTMLFile( '/Users/patrick/DevWork/websites/wordpress_ms_3_8_1/GDDN/26181968.html', LIBXML_HTML_NOIMPLIED );

$simple_xml = simplexml_import_dom( $doc );

print_r( $simple_xml );
changeBodyToDiv( $simple_xml );


readxml( $simple_xml );
