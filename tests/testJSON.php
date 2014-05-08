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

$returnValue = preg_replace('/(\w):/', '"$1":', "{'/Content/User_Guide/Compare-page.html':{i:[2],t:['Compare-page'],b:['']},'/Content/User_Guide/Comparing.html':{i:[17],t:['Comparing'],b:['']},'/Content/User_Guide/Edit-Site-page.html':{i:[4],t:['Edit-Site-page'],b:['']},'/Content/User_Guide/Editor-page.html':{i:[3],t:['Editor-page'],b:['']},'/Content/User_Guide/Exporting.html':{i:[18],t:['Exporting'],b:['']},'/Content/User_Guide/Getting-started.html':{i:[0],t:['Getting-started'],b:['']},'/Content/User_Guide/Git-Commit-page.html':{i:[5],t:['Git-Commit-page'],b:['']},'/Content/User_Guide/Git-Log-page.html':{i:[6],t:['Git-Log-page'],b:['']},'/Content/User_Guide/Git-Status-page.html':{i:[7],t:['Git-Status-page'],b:['']},'/Content/User_Guide/Importing.html':{i:[19],t:['Importing'],b:['']},'/Content/User_Guide/Login-page.html':{i:[8],t:['Login-page'],b:['']},'/Content/User_Guide/Navigating-and-searching.html':{i:[20],t:['Navigating-and-searching'],b:['']},'/Content/User_Guide/Operations-page.html':{i:[9],t:['Operations-page'],b:['']},'/Content/User_Guide/Profile-page.html':{i:[10],t:['Profile-page'],b:['']},'/Content/User_Guide/Repositories-page.html':{i:[11],t:['Repositories-page'],b:['']},'/Content/User_Guide/Search-Results-page.html':{i:[12],t:['Search-Results-page'],b:['']},'/Content/User_Guide/Settings-page.html':{i:[13],t:['Settings-page'],b:['']},'/Content/User_Guide/Shell-page.html':{i:[14],t:['Shell-page'],b:['']},'/Content/User_Guide/Sites-page.html':{i:[15],t:['Sites-page'],b:['']},'/Content/User_Guide/Working-with-Git.html':{i:[22],t:['Working-with-Git'],b:['']},'/Content/User_Guide/Working-with-folders-and-files.html':{i:[21],t:['Working-with-folders-and-files'],b:['']},'___':{i:[1,16],t:['Reference','How-To Guides'],b:['','']}}", -1, $count);

//$returnValue = preg_replace('/(\\w*):/U', '"$1":', '{numchunks:1,prefix:\'Toc_Chunk\',chunkstart:[\'/Content/gdn-ConditionalExpressions.html\'],tree:{n:[{i:0,c:0},{i:1,c:0,n:[{i:2,c:0},{i:3,c:0},{i:4,c:0,n:[{i:5,c:0,n:[{i:6,c:0}]},{i:7,c:0},{i:8,c:0}]}]},{i:9,c:0,n:[{i:10,c:0}]}]}}', -1, $count);

$jsonString = str_replace("'", "\"", $returnValue);


$test = json_decode($jsonString, true);
processN($test['tree']['n'], 0);
