<?php

// $start = 7;
// $max = 7;


$scriptPath = dirname(__FILE__);
$new_files_dir = "$scriptPath/usr/FinalOutput";
$new_files_dir = "$scriptPath/usr/images";


$path_to_wiki = "C:/xampp/htdocs/wiki/mod";

$page_comment = "Robopedia import";

$imageExtensions = "svg png jpg jpeg gif bmp SVG PNG JPG JPEG GIF BMP";



$files = scandir($new_files_dir);
foreach($files as $key => $filename) {
	echo "$filename";

	if ( isset($start) && $key<$start ) {
		echo " - skipped\n\n";
		continue;
	}
	if ( isset($max) && $key>=$max ) {
		echo " - skipped\n\n";
		break;
	}
	
	if ( is_file($new_files_dir.'/'.$filename) && substr($filename, -4) == 'wiki' ) {
		$title = substr($filename, 0, -5); // remove ".wiki" 
		echo " (file) - Wiki filename: $title";
	
		// import page
		$cmd = "php $path_to_wiki/maintenance/importTextFile.php --conf $path_to_wiki/LocalSettings.php ";
	
		if ($title)
			$cmd .= "--title \"$title\" ";
	
		if ($page_comment)
			$cmd .= "--comment \"$page_comment\" ";
			
		$cmd .= "\"$new_files_dir/$filename\"";
	
		// echo "$cmd\n\n";
		shell_exec( $cmd );
	
	}
	else {
		echo " (directory)";
	}
	echo "\n\n";
}


// import image
$cmd = "php $path_to_wiki/maintenance/importImages.php --conf $path_to_wiki/LocalSettings.php $new_images_dir $imageExtensions";
// shell_exec( $cmd );

//php C:/xampp/htdocs/wiki/oso/maintenance/importImages.php --conf C:/xampp/htdocs/wiki/oso/LocalSettings.php C:/xampp/htdocs/curl/image-test svg png jpg jpeg gif bmp SVG PNG JPG JPEG GIF BMP