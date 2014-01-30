<?php
/**
 *  This file uses PHP cURL to pull the contents of a particular Sharepoint wiki
 *  page. 
 *
 *
 **/


require "../LocalSettings.php";

$pagecontent = $_POST['pagecontent'];
$pagetitle = $_POST['pagetitle'];


if (!file_exists('../usr/sharepoint-content')) {
    mkdir('../usr/sharepoint-content', 0777, true);
}
file_put_contents("../usr/sharepoint-content/$pagetitle.html", $pagecontent, FILE_USE_INCLUDE_PATH); 


$response = array(
	"message" => "$pagetitle - Wiki content saved - " . date("H:i:s", time()),
);

echo json_encode($response);