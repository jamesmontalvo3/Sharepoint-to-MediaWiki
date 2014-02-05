<?php
/**
 *
 **/

require "../LocalSettings.php";

if (isset($_POST['pagecontent']))
	$pagecontent = $_POST['pagecontent'];
else
	$pagecontent = "";
$pagetitle = $_POST['pagetitle'];

$sharepointHtmlPath = dirname(__FILE__) . "/../usr/sharepoint-content";
$cleanHtmlPath = dirname(__FILE__) . "/../usr/cleanHTML";

// create directories for raw sharepoint HTML and LibreOffice-sanitized HTML
if (!file_exists( $sharepointHtmlPath )) {
    mkdir($sharepointHtmlPath, 0777, true);
}
if (!file_exists( $cleanHtmlPath )) {
    mkdir($cleanHtmlPath, 0777, true);
}

// Save sharepoint HTML to disk
file_put_contents("$sharepointHtmlPath/$pagetitle.html", $pagecontent, FILE_USE_INCLUDE_PATH); 

# Convert to clean HTML using headless LibreOffice
#
# EXAMPLE: "C:/Program Files (x86)/LibreOffice 4/program/soffice.exe" --headless --convert-to html:HTML -outdir C:/sofficeTest/Clean C:/sofficeTest/Dirty/ACDB.html
exec("\"$sofficePath\" --headless --convert-to html:HTML -outdir \"$cleanHtmlPath\" \"$sharepointHtmlPath/$pagetitle.html\"");

// soffice command doesn't give any positive feedback on success. Check if file exists.
if ( file_exists( "$cleanHtmlPath/$pagetitle.html" )) {
	$response = array(
		"message" => "$pagetitle - Clean HTML saved - " . date("H:i:s", time()),
		"status"  => "success",
	);
} else {
	$response = array(
		"message" => "$pagetitle - Failed to save clean HTML - " . date("H:i:s", time()),
		"status"  => "fail",
	);
}

echo json_encode($response);