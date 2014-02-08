<?php
require "../LocalSettings.php";

$url = $_GET['url'];
$filename = explode("/", $url);
$filename = $filename[ count($filename)-1 ];
$filename = str_replace("%20", "_", $filename);
$messages = array();

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

// if an error occurred
if ($ch->errno > 0 || $ret == "") {
	$messages[] = "Error {$ch->errno} occurred: {$ch->error}. Reattempting...";
	
	// retry retrieving image
	usleep(250000); // pause execution for 250 ms in case overloading Sharepoint server
	$ch = new Sharepoint_cURL( // not sure if this is required...
		$url,
		$loginDomain . "/" . $loginUser . ":" . $loginPass
	);
	$ret = $ch->exe();
	if ($ch->errno > 0 || $ret == "") {
		$messages[] = "Another error occurred (#{$ch->errno}): {$ch->error}";
		$messages[] = "Skipping $filename";
		$messages[] = "URL: $url";
		$ret = false;
	}
	
}


/**
 * Should be using exif_imagetype() to determine if the file is an image
 * 
 * Note: this will emit an E_NOTICE and return FALSE if it is unable to read 
 * enough bytes from the file to determine the image type. So do:
 *
 *	try {
 *		if ( exif_imagetype() === false )
 *			$is_image = false;
 *		else
 *			$is_image = true;
 *	}
 *	catch (Exception $e) {
 *		$is_image = false;
 *		$messages[] = "Data received is not an image...blah blah blah...";
 *	}
 *
 **/
if ( $ret !== false ) {

	if (!file_exists('../usr/images')) {
		mkdir('../usr/images', 0777, true);
	}
	file_put_contents("../usr/images/$filename", $ret, FILE_USE_INCLUDE_PATH); 

	$messages[] = "<a href='$url'>$filename</a> - " . date("H:i:s", time());
	$success = true;
}
else {
	$success = false;
}

if (count($messages) > 1) {
	$message = "<ul><li>".implode("</li><li>", $messages)."</li></ul>";
} else {
	$message = $messages[0];
}
 
$response = array(
	"message" => $message,
	"success" => $success,
);
echo json_encode($response);
