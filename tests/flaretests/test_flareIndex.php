<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-09-22
 * Time: 8:36 PM
 */
require_once "./../../public/includes/indices/FlareWebsiteIndex.php";
require_once "./../../public/includes/retriever/LocalFileRetriever.php";
require_once dirname( __FILE__ ) . "/../../includes/LinkedTree.php";


$localFileRetriever = new \droppedbars\files\LocalFileRetriever('./');
$flareIndex = new \html_import\indices\FlareWebsiteIndex($localFileRetriever);
$flareIndex->buildHierarchyFromWebsiteIndex();

$counter = 0;
$node = $flareIndex->getNextHTMLFile();
while (!is_null($node)) {
	echo $counter.' '.$node->payload()."\n";
	$counter++;
	$node = $flareIndex->getNextHTMLFile();
}