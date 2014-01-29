<?php

$url = $_GET['url'];
$page = $_GET['page'];

$path_sep = '\\';

echo "<li><a href='$url'>$page</a>";

$cookie_file_path = dirname(__FILE__) . $path_sep . 'cookies.txt';
$ch = curl_init();

$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36";


/**
 *  The following sets the $username and $password
 *  You can also set the $user_agent value to something like 
 *  "Sharepoint Thief" if that makes you happy.
 **/
require "../usr/cURL_credentials.php";

//==========================================================================
curl_setopt($ch, CURLOPT_USERPWD, $username. ':' . $password);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_FAILONERROR, 0);
curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
//==========================================================================


try {
	$ret = curl_exec($ch);
} catch (Exception $e) {
	die("Curl failed: " . $e->getMessage() );
}
file_put_contents("./raw-html/$page.html", $ret, FILE_USE_INCLUDE_PATH); 

echo " - " . date("H:i:s", time());
echo "</li>";