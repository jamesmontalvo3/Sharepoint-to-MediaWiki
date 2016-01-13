<?php
/**
 *
 *
 **/

require "../LocalSettings.php";

$usr = dirname(__FILE__) . "/../usr";
$infile = "$usr/pages.xls";
$outfile = "$usr/pages.html";


if ( ! file_exists( $infile ) ) {
	$infile .= "x"; // make it a .xlsx
	if ( ! file_exists( $infile ) ) {
		die( "need a pages.xls or pages.xlsx file" );
	}
}



// C:\Program Files (x86)\LibreOffice 4>"C:/Program Files (x86)/LibreOffice 4/program/soffice.exe" --headless --convert-to html --outdir C:/code/soffice/ C:/code/soffice/Robopedia.xls

exec("\"$sofficePath\" --headless --convert-to html -outdir \"$usr\" \"$infile\"");


if ( file_exists( $outfile )) {

	$outfile = file_get_contents( $outfile );

	$response = array(
		"message" => "Page list retrieved - " . date("H:i:s", time()),
		"pageHTML" => $outfile,
		"success"  => true,
	);
} else {
	$response = array(
		"message" => "Failed to retrieve page list - " . date("H:i:s", time()),
		"pageHTML" => "",
		"success"  => false,
	);
}

echo json_encode($response);
