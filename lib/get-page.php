<?php
/**
 *  This file uses PHP cURL to pull the contents of a particular Sharepoint wiki
 *  page. 
 *
 *
 **/


require "../LocalSettings.php";

$url = $_GET['url'];
$page = $_GET['page'];


/**
 *  The following sets the $username and $password
 *  You can also set the $user_agent value to something like 
 *  "Sharepoint Thief" if that makes you happy.
 **/
 
require_once "Sharepoint_cURL.php";
$ch = new Sharepoint_cURL(
	$url,
	$loginDomain . "/" . $loginUser . ":" . $loginPass
);
$pageHTML = $ch->exe();

$response = array(
	"pagetitle" => $page,
	"message" => "<a href='$url'>$page</a> - Raw HTML received - " . date("H:i:s", time()),
	"pageHTML" => $pageHTML,
);

echo json_encode($response);