<?php
$url = $_GET['url'];
$page = $_GET['page'];

echo "<li><a href='$url'>$page</a>";


$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36";


/**
 *  The following sets the $username and $password
 *  You can also set the $user_agent value to something like 
 *  "Sharepoint Thief" if that makes you happy.
 **/
 
require_once "Sharepoint_cURL.php";
require "../usr/cURL_credentials.php";
$ch = new Sharepoint_cURL(
	$url,
	$loginDomain . "/" . $loginUser . ":" . $loginPass
);
$ret = $ch->exe();


if (!file_exists('../usr/2-raw-html')) {
    mkdir('../usr/2-raw-html', 0777, true);
}
file_put_contents("../usr/2-raw-html/$page.html", $ret, FILE_USE_INCLUDE_PATH); 

echo " - " . date("H:i:s", time());
echo "</li>";