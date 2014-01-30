<?php
require "../LocalSettings.php";

$url = $_GET['url'];
$filename = explode("/", $url);
$filename = $filename[ count($filename)-1 ];
$filename = str_replace("%20", "_", $filename);

echo "<li><a href='$url'>$filename</a>";

if ( strpos($url, "http") !== 0 ) {
	$url = "https://modspops.jsc.nasa.gov" . $url;
} else {
	echo " - skipped, not local image</li>";
}


require_once "Sharepoint_cURL.php";
$ch = new Sharepoint_cURL(
	$url,
	$loginDomain . "/" . $loginUser . ":" . $loginPass
);
$ret = $ch->exe();


if (!file_exists('../usr/4-images')) {
    mkdir('../usr/4-images', 0777, true);
}
file_put_contents("../usr/4-images/$filename", $ret, FILE_USE_INCLUDE_PATH); 

// try {
	// if( shell_exec( 'phantomjs C:/xampp/htdocs/curl/clean.js "' . $page . '.html"' ) )
		// echo " - PhantomJS success";
	// else
		// echo " - PhantomJS failure";
// } catch (Exception $e) {
	// echo "PhantomJS failed on $page with message: " . $e->getMessage();
// }

echo " - " . date("H:i:s", time());
echo "</li>";