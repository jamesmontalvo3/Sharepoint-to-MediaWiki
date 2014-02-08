Sharepoint-to-MediaWiki
=======================

Pull content out of Sharepoint wiki for insertion into MediaWiki

## Requirements
1. Web server with PHP (I used XAMPP)
2. Perl (I used Strawberry Perl) with HTML-WikiConverter module and html2wiki in path
3. LibreOffice installed for command-line conversion

## Steps

1. Clone this repo into your webserver root. So you can access the scripts at http://localhost/Sharepoint-to-MediaWiki
2. Go to the Sharepoint wiki you want to convert, and find the list of pages. Somewhere on this page it allows you to download the list to an Excel document. Do that. Sorry, different versions of Sharepoint put this link in different places, and I haven't had a chance to document them all yet. Put this Excel file into the "usr" directory of Sharepoint-to-MediaWiki, and rename it "pages.xls".
3. Copy LocalSettings-default.php and rename it to LocalSettings.php. Fill in the settings as required. (Sorry, I'll put more info here later)
4. Navigate to http://localhost/Sharepoint-to-MediaWiki/1-get-content.php. This page will run many commands via AJAX requests. Depending on the size of the Sharepoint wik this could take awhile. Once it's done you'll have all the pages in the usr/FinalOutput directory and the images in the usr/images directory
5. Verify all the pages look the way you want them to. If they don't...well at this point I haven't written any options into the wikitext processing. So you can help me fix it!
6. On the command line, navigate to the Sharepoint-to-MediaWiki directory, then run "php 2-push-to-wiki.php". This will 
