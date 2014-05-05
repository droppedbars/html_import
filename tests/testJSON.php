<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-05-04
 * Time: 7:22 AM
 */

function processN(Array $anN, $indent) {
	foreach ($anN as $value) {
		$iValue = $value['i'];
		for ($i = 0; $i < $indent; $i++) echo ' ';
		echo $iValue."\n";
		// TODO: Process this one
		if (array_key_exists('n', $value)) {
			processN($value['n'], $indent+2);
		}
	}
}

$count = null;
$returnValue = preg_replace('/(\\w*):/U', '"$1":', '{numchunks:1,prefix:\'Toc_Chunk\',chunkstart:[\'/Content/gdn-ConditionalExpressions.html\'],tree:{n:[{i:0,c:0},{i:1,c:0,n:[{i:2,c:0},{i:3,c:0},{i:4,c:0,n:[{i:5,c:0,n:[{i:6,c:0}]},{i:7,c:0},{i:8,c:0}]}]},{i:9,c:0,n:[{i:10,c:0}]}]}}', -1, $count);

$jsonString = str_replace("'", "\"", $returnValue);


$test = json_decode($jsonString, true);
processN($test['tree']['n'], 0);
