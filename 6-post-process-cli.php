﻿<?php

function text_process ($old_text) {

	// replace HTML breaks with double newlines
	$new_text = str_replace("<br />", "\n\n", $old_text);
	
	// remove weird question marks
	$new_text = str_replace("�", "", $new_text);
	
	$patterns[0] = "/<\/font>/";
	$replaces[0] = "";

	$patterns[1] = "/<font[^>]+>/";
	$replaces[1] = "";

	// $patterns[2] = "/(\[/INTERNAL-WIKI-LINK)([^\s*])(\s)([^\]*])(\])/";
	// $replaces[2] = "[[\4]]";
	$patterns[2] = "/(\[\/INTERNAL-WIKI-LINK)(\S*)(\s+)([^\]]*)(\])/";
	$replaces[2] = '[[\4]]';

	$patterns[3] = "/%20/";
	$replaces[3] = " ";
	
	$patterns[4] = '/style="[^\"]*"/'; // match /style="..."/ where "..."=anything except double quotes
	$replaces[4] = "";

	$patterns[5] = '/{\|/'; // match {|
	$replaces[5] = "{| class=\"wikitable\" ";

	// remove single lines of bold text, where text less than 60 characters
	$patterns[6] = "/(^|\n)(''')(\s*)([^\n^']{0,60})(''')(\s*)(\n|)/"; 
	$replaces[6] = "\n" . '=== \4 ===' . "\n"; // replace with level 3 heading

	
	$new_text = preg_replace($patterns, $replaces, $new_text);
	
	// echo "<h1>Old Text</h1>";
	// echo "<textarea type='text' style='width:600px;height:300px;'>$old_text</textarea>";

	// echo "<h1>New Text</h1>";
	// echo "<textarea type='text' style='width:600px;height:300px;'>$new_text</textarea>";

	return $new_text;
    
}

$pages = json_decode( file_get_contents("sp-pages.json" , true), true );

foreach($pages as $page => $url) {

	$save = file_put_contents(
		"C:/xampp/htdocs/curl/wiki-files-final/$page.wiki",
		text_process(
			file_get_contents("C:/xampp/htdocs/curl/wiki-files/$page.wiki")
		)
	);

	if ($save !== false) {
		echo "$page.wiki created ($save bytes written)\n";
	} else {
		echo "$page.wiki failed to be created\n";
	}
}

echo "\nCOMPLETE!";