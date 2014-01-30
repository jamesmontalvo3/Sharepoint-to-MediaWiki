<?php
error_reporting( -1 );
ini_set( 'display_errors', 1 );

$url = $_GET['url'];
$page = $_GET['page'];


echo "<li><a href='$url'>$page</a>";

$ch = curl_init();

$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36";


/**
 *  The following sets the $username and $password
 *  You can also set the $user_agent value to something like 
 *  "Sharepoint Thief" if that makes you happy.
 **/
require "../usr/cURL_credentials.php";
$login = $loginDomain . "/" . $loginUser . ":" . $loginPass;


// Thanks to: http://www.tunnelsup.com/using-the-sharepoint-2013-wiki-api
//==========================================================================
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);    // Optional only if the sharepoint requires authentication
curl_setopt($ch, CURLOPT_USERPWD, $login);            // Optional only if the sharepoint requires authentication
curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//==========================================================================

try {
	$ret = curl_exec($ch);
} catch (Exception $e) {
	die("Curl failed: " . $e->getMessage() );
}

if (!file_exists('../usr/2-raw-html')) {
    mkdir('../usr/2-raw-html', 0777, true);
}
file_put_contents("../usr/2-raw-html/$page.html", $ret, FILE_USE_INCLUDE_PATH); 

echo " - " . date("H:i:s", time());
echo "</li>";