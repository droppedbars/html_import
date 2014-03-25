<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-03-23
 * Time: 8:45 PM
 */

function processNode(SimpleXMLElement $node, $higher_counter) {
	$i = 0;
	foreach ($node->children() as $level0) {
		if (strcmp('li', $level0->getName()) == 0) {
			foreach ($level0->children() as $level1) {
				if (strcmp('a', $level1->getName()) == 0) {
					echo "\n".'<document title="'.$level1.'" src="'.$level1->xpath('@href')[0].'" category="documentation" tag="foo" order="'.$higher_counter.'">';
					//echo $level1->xpath('@href')[0].' '.$level1."\n";
					$i++;
					$higher_counter++;
				} else if (strcmp('ul', $level1->getName()) == 0) {
					processNode($level1, $i);
					$i++;
				}
			}
			echo "\n</document>";

		}
	}
}

$doc                      = new DOMDocument();
$doc->strictErrorChecking = false;
libxml_use_internal_errors( true ); // some ok HTML will generate errors, this masks them, pt 1/2
$doc->loadHTMLFile( './index.html', LIBXML_HTML_NOIMPLIED );
libxml_clear_errors(); // some ok HTML will generate errors, this masks them, pt 2/2
$file_as_xml_obj = simplexml_import_dom( $doc );

$menu_div = $file_as_xml_obj->xpath( "//div[@class='pageSection']/ul");

echo "\n".'<?xml version="1.0" encoding="utf-8"?> <knowledgebase version="1.0">';
processNode($menu_div[0], 0);
echo "\n".'</knowledgebase>';