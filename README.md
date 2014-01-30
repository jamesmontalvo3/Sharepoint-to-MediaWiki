Sharepoint-to-MediaWiki
=======================

Pull content out of Sharepoint wiki for insertion into MediaWiki

Before you go any further, a warning: This is freakin' insane. It is the ugliest, kludgiest thing I've ever created. To get this working on my Windows 7 laptop I had to stitch together PHP, PhantomJS, command line mimicked in the browser via AJAX, a virtual machine, Perl, and I threw in some Python because...because at that point why the hell not...

This pulls the content from Sharepoint in many steps, with the intent of it being modular. Some people may not require certain steps, or may want to add different post-processing. At some point I'd like to make it into a MediaWiki extension.


## Requirements
1. Windows 7
2. Web server with PHP (I used XAMPP)
3. VirtualBox (or equivalent) with Linux installation (I used Ubuntu 12.04) and:
  1. Perl with HTML-WikiConverter
  2. LibreOffice installed, with "soffice" in path
4. A strong desire to abandon Sharepoint

## Steps

There are a lot of steps that the user must perform to get this to work. I would have preferred to write this such that you type one command into the command line and wait for it to churn through, but unfortunately Windows, PHP, libcurl, LibreOffice and Perl hate me.

**Note: Right now this code does not work, as I'm refactoring many individual scripts into a cohesive project. All the required files are there, but I changed file names and moved some things around so references are probably broken.**

1. (incomplete) Get list of Sharepoint Wiki pages
  1. For now doing this manually, using Chrome JS console and the JS in get-sp-pages.js
  2. Write to "./output/1-sp-pages.json"
2. Get the HTML and images from Sharepoint
  1. Navigate to the file "2-get-content.php" in your web browser.
  2. This will run many commands via AJAX requests
  3. HTTP requests to get-page.php uses cURL and NTLM to get pages from Sharepoint
    1. Could not use PHP on command line with cURL, which is perhaps a Windows issue
    2. Could not use PHP with cURL and NTLM on Linux at all, unless you downgraded to an earlier version of libcurl.
  4. HTML of each pages is sent back to your browser, where:
    1. The fluff is cropped out, and only the relevant wiki content is kept
	2. Each link in the content is marked with /INTERNAL-WIKI-LINK or /INTERNAL-WIKI-FILE-LINK so proper wiki links can be created later
	2. The content is sent back to your webserver and saved as a .html file
    3. URLs of each image are recorded for later download
  5. Once all HTML is saved to the server, all images are downloaded
3. IN A LINUX VIRTUAL MACHINE, run 5-convert-to-wiki.py, which:
  1. Uses LibreOffice command line to convert HTML-to-HTML. That is, it takes ugly Microsoft HTML and converts into something cleaner.
  2. Uses the Perl HTML-WikiConverter to turn HTML into MediaWiki wikitext.
  3. The intermediate files from LibreOffice are written to "./output/5-libre-wiki-html"
  4. The MediaWiki files are written to "./output/6-wiki-files"
  5. The files need to be moved back and forth between host and client OS unless you setup a shared folder that both OSs can write to
6. MOVE FILES BACK TO WINDOWS, run post-process-cli.php
  1. Removes <font> tags, which generally make the output look like crap. They make some fonts giant, some small. Ideally I'd like to leave behind <font color="..."> where applicable, but I'm not smart enough on RegExps for that. Mostly I didn't know how to leave behind the matching </font>.
  2. Converts links that should be internal wiki links to double-bracket links
  3. Removes all "style='...'", since this is generally unnecessary and makes the wikitext really bloated
  4. Add class="wikitable" to all tables (required once you strip style="...")
  5. Convert any single-line bold text to a level-3 header
  6. Save new files to "./output/7-wiki-files-final"
7. Push images and pages to your wiki with push-to-wiki.php ON THE COMMAND LINE.
  1. Edit this file to point to your wiki
  2. It calls the maintenance scripts importTextFile.php and importImages.php

## Questions and Future Development

1. Could the things that gave me trouble in either Windows or Linux be performed on OSX? Could it act as a happy medium?
2. Fix the main sticking points:
  1. cURL in PHP on Windows doesn't want to run on the command line
  2. I couldn't get Perl configured with HTML-WikiConverter on Windows
  3. I ran into some trouble with LibreOffice on the command line on Windows and gave up...could it be done?
3. What would it take to get PhantomJS to support NTLM?
