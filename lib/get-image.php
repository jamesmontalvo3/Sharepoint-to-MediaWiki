<?php

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

$path_sep = '\\';


$cookie_file_path = dirname(__FILE__) . $path_sep . 'cookies.txt';
$ch = curl_init();

$username = "ejmontal";
require "pw.php"; //sets password in another file so I don't have to keep it up on my screens

// $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36";
$user_agent = "EVA Web Robot";

// $url = "https://mod2.jsc.nasa.gov/wiki/fetch/index.php";
// $url = "https://modspops.jsc.nasa.gov/MOD/DX/DX22/MSSDOC/ROBOpedia/Wiki%20Pages/TUS%20Cable.aspx";
// $url = "https://modspops.jsc.nasa.gov/MOD/DX/DX22/MSSDOC/ROBOpedia/Wiki%20Pictures/TUS_Cable_Photo.jpg";

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
//header('Content-type:image/jpg');
//echo $ret; // page content

file_put_contents("./images/$filename", $ret, FILE_USE_INCLUDE_PATH); 

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