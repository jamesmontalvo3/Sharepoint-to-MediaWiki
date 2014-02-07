<?php
/**
 *
 **/

require "../LocalSettings.php";

if (isset($_POST['imageList']))
	$imageList = $_POST['imageList'];
else
	$imageList = "";

$imageListPath = dirname(__FILE__) . "/../usr/imageList.json";

file_put_contents("$imageListPath", $imageList, FILE_USE_INCLUDE_PATH); 

if ( $imageList ) {
	$response = array(
		"message" => "Image list saved - " . date("H:i:s", time()),
		"success"  => true,
	);
} else {
	$response = array(
		"message" => "No image list content received - " . date("H:i:s", time()),
		"success"  => false,
	);
}

echo json_encode($response);