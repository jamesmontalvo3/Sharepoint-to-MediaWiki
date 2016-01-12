<?php

// the domain used to reach the Sharepoint wiki
$loginDomain = 'ABC';

// the username used ot reach the Sharepoint wiki 
$loginUser = 'johndoe';

// SECURITY RISK: You need to save your password used to reach the 
// Sharepoint wiki here. Sorry, it was the easiest way to create this
// converter. Don't use this on shared systems, and delete your password
// when you're done. Consider changing your password after, too.
$loginPass = 'password';

// LibreOffice is required to run this converter, and the computer used
// needs to know the location of the LibreOffice command line utility.
$sofficePath = "C:/Program Files (x86)/LibreOffice 4/program/soffice.exe";

// The MediaWiki install path for the wiki you want to push content to. It
// is recommended you test this on a non-production wiki first, or even push
// first to an intermediate wiki, verify results, then do an XML export. Then
// import the XML into the production wiki.
$path_to_wiki = '/var/www/wiki';

// Replace with text if a specific comment is desired for each page import.
// This will mean that the "edit" which creates the page will have a summary
// like "imported content from ABC Sharepoint Wiki"
// 
// Known bugs:
//  * Don't use double quotes within comment. Single quotes are fine.
$page_comment = false;

// If you'd like all pages to have a common prefix, set this value to text.
// This can be handy if importing content from a smaller SharePoint wiki into
// an existing larger MediaWiki. If, for example, you were importing your
// accounting office's wiki into the greater corporate wiki, you may set a 
// prefix like 'Accounting/'. The forward slash at the end will make all of
// the imported pages into sub-pages of the "Accounting" page. This can be
// useful even if it is only temporary while you figure out how to rename
// Accounting's pages to fit into your new larger wiki.
//
// Note: this only applies to page names, not file names.
//
// Example: $titlePrefix = 'Accounting/';
$titlePrefix = false;

// If you'd like to give these page contributions (all the imports) to a
// particular user (e.g. yourself) then set this to your username.
// Example: $contributingUser = 'Johndoe';
$contributingUser = false;