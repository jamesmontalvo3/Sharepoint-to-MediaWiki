<?php

class PostProcess {

	static public function process ($old_text, $morePatterns=array(), $moreReplaces=array() ) {

		global $titlePrefix;

		if ( ! $titlePrefix ) {
			$titlePrefix = '';
		}

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
		$replaces[2] = '[[' . $titlePrefix . '\4]]';

		$patterns[3] = "/%20/";
		$replaces[3] = " ";

		$patterns[4] = '/style="[^\"]*"/'; // match /style="..."/ where "..."=anything except double quotes
		$replaces[4] = "";

		$patterns[5] = '/{\|/'; // match {|
		$replaces[5] = "{| class=\"wikitable\" ";

		// remove single lines of bold text, where text less than 60 characters
		$patterns[6] = "/(^|\n)(''')(\s*)([^\n^']{0,60})(''')(\s*)(\n|)/";
		$replaces[6] = "\n" . '=== \4 ===' . "\n"; // replace with level 3 heading

		// allow custom pattern replacements
		if ( count( $moreReplaces ) > 0
			&& count( $morePatterns ) > 0
			&& count( $moreReplaces ) === count( $morePatterns )
			) {

			$patterns = array_merge( $patterns, $morePatterns );
			$replaces = array_merge( $replaces, $moreReplaces );

		}

		$new_text = preg_replace($patterns, $replaces, $new_text);

		// echo "<h1>Old Text</h1>";
		// echo "<textarea type='text' style='width:600px;height:300px;'>$old_text</textarea>";

		// echo "<h1>New Text</h1>";
		// echo "<textarea type='text' style='width:600px;height:300px;'>$new_text</textarea>";

		return $new_text;

	}

}