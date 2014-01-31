<?php
require "../LocalSettings.php";

$url = $_GET['url'];
$filename = explode("/", $url);
$filename = $filename[ count($filename)-1 ];
$filename = str_replace("%20", "_", $filename);


if ( strpos($url, "http") !== 0 ) {
	$url = "https://modspops.jsc.nasa.gov" . $url;
} else {
	$response = array(
		"message" => "<a href='$url'>$filename</a> - <span style='color:red;'>skipped, not local image</span> - " . date("H:i:s", time()),
	);
	echo json_encode($response);
	exit();
}


require_once "Sharepoint_cURL.php";
$ch = new Sharepoint_cURL(
	$url,
	$loginDomain . "/" . $loginUser . ":" . $loginPass
);
$ret = $ch->exe();


if (!file_exists('../usr/images')) {
    mkdir('../usr/images', 0777, true);
}
file_put_contents("../usr/images/$filename", $ret, FILE_USE_INCLUDE_PATH); 


$response = array(
	"message" => "<a href='$url'>$filename</a> - " . date("H:i:s", time()),
);
echo json_encode($response);
