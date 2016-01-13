<?php
/**
 *
 **/

// initialize prior to LocalSettings.php
$userDefinedPatterns = array();
$userDefinedReplaces = array();

require "../LocalSettings.php";
require_once "PostProcess.php";

function createDir ($path) {
	if (!file_exists( $path )) {
		mkdir($path, 0777, true);
	}
}

if (isset($_POST['pagecontent']))
	$pagecontent = $_POST['pagecontent'];
else
	$pagecontent = "";
$pagetitle = $_POST['pagetitle'];

$saveDir = dirname(__FILE__) . "/../usr";
$tmpDir = $saveDir . "/tmp";

// temporary directories
$sharepointHtmlPath = "$tmpDir/sharepoint-content";
$cleanHtmlPath = "$tmpDir/cleanHTML";
$wikitextOutput = "$tmpDir/WikitextOutput";

// final output location
$finalOutput = "$saveDir/FinalOutput";

$messages = array();

// create directories
createDir( $tmpDir );
createDir( $sharepointHtmlPath );
createDir( $cleanHtmlPath );
createDir( $wikitextOutput );
createDir( $finalOutput );

// Save sharepoint HTML to disk
file_put_contents("$sharepointHtmlPath/$pagetitle.html", $pagecontent, FILE_USE_INCLUDE_PATH);

# Convert to clean HTML using headless LibreOffice
#
# EXAMPLE: "C:/Program Files (x86)/LibreOffice 4/program/soffice.exe" --headless --convert-to html:HTML -outdir C:/sofficeTest/Clean C:/sofficeTest/Dirty/ACDB.html
exec("\"$sofficePath\" --headless --convert-to html:HTML -outdir \"$cleanHtmlPath\" \"$sharepointHtmlPath/$pagetitle.html\"");

// soffice command doesn't give any positive feedback on success. Check if file exists.
if ( file_exists( "$cleanHtmlPath/$pagetitle.html" )) {
	$messages[] = "$pagetitle - LibreOffice-sanitized HTML saved - " . date("H:i:s", time());
	$success = true;
} else {
	$messages[] = "<span style='color:red;'>$pagetitle - Failed to save clean HTML - " . date("H:i:s", time()) . "</span>";
	$success = false;
}

# Convert HTML to Wikitext using html2wiki
#
#
exec("html2wiki --dialect MediaWiki \"$cleanHtmlPath/$pagetitle.html\" > \"$wikitextOutput/$pagetitle.wiki\"");

if ( file_exists( "$wikitextOutput/$pagetitle.wiki" )) {
	$messages[] = "$pagetitle - Wikitext saved - " . date("H:i:s", time());
	$success = true;
} else {
	$messages[] = "<span style='color:red;'>$pagetitle - Failed to save wikitext - " . date("H:i:s", time()) . "</span>";
	$success = false;
}

# Do some post-processing of the wikitext to account for common Sharepoint practices
#
#
$save = file_put_contents(
	"$finalOutput/$pagetitle.wiki",
	PostProcess::process(
		file_get_contents("$wikitextOutput/$pagetitle.wiki"),
		$userDefinedPatterns,
		$userDefinedReplaces
	)
);


if ($save !== false) {
	$messages[] = "$pagetitle - Final output saved ($save bytes written)";
	$success = true;
} else {
	$messages[] = "<span style='color:red;'>$pagetitle - Final output failed to be created</span>";
	$success = false;
}


if (count($messages) > 1) {
	$message = "<strong>$pagetitle actions:</strong><ul><li>" . implode("</li><li>", $messages) . "</li></ul>";
}
else {
	$message = $messages[0];
}

$response = array(
	"message" => $message,
	"success" => $success,
);

echo json_encode($response);
