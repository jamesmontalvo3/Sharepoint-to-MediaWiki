Sharepoint-to-MediaWiki
=======================

Pull content out of Sharepoint wiki for insertion into MediaWiki

Before you go any further, a warning: This is freakin' insane. It is the ugliest, kludgiest thing I've ever created. To get this working on my Windows 7 laptop I had to stitch together PHP, PhantomJS, command line mimicked in the browser via AJAX, a virtual machine, Perl, and I threw in some Python because...because at that point why the hell not...

This pulls the content from Sharepoint in many steps, with the intent of it being modular. Some people may not require certain steps, or may want to add different post-processing. At some point I'd like to make it into a MediaWiki extension.


## Requirements
1. Windows 7
2. Web server with PHP (I used XAMPP)
3. PhantomJS
4. VirtualBox (or equivalent) with Linux installation (I used Ubuntu 12.04) and:
  1. Perl with HTML-WikiConverter
  2. LibreOffice installed, with "soffice" in path
5. A strong desire to abandon Sharepoint

## Steps

There are a lot of steps that the user must perform to get this to work. I would have preferred to write this such that you type one command into the command line and wait for it to churn through, but unfortunately Windows, PHP, libcurl, LibreOffice and Perl hate me.

**Note: Right now this code does not work, as I'm refactoring many individual scripts into a cohesive project. All the required files are there, but I changed file names and moved some things around so references are probably broken.**

1. (incomplete) Get list of Sharepoint Wiki pages
  1. For now doing this manually, using Chrome JS console and the JS in get-sp-pages.js
  2. Write to "./output/1-sp-pages.json"
2. Get HTML of each page
  1. Web page with AJAX requests to "get-page.php"
  2. get-page.php uses cURL and NTLM to get page
  3. Could not use PHP on command line with cURL, which is perhaps a Windows issue
  4. Could not use PHP with cURL and NTLM on Linux at all, unless you downgraded to an earlier version of libcurl.
  5. Write each HTML file to "./output/2-sp-html" directory
3. Use PhantomJS by running command "phantomjs 3-analyze-pages.js":
  1. Get just the HTML of the content portion of the Sharepoint page
  2. To each intra-wiki link, prepend "/INTERNAL-WIKI-LINK" so those links can be turned into internal links later (i.e. [[My Link]] instead of [http://example.com/page1 My Link])
  3. Do the same for intra-wiki links to files, except using "/INTERNAL-WIKI-FILE-LINK"
  4. Create a list of all images used on all pages and write that list to "./output/4-images.txt"
  5. Write the wiki content of each page to "./output/3-sp-wiki-html" directory
4. Download all images using get-images.php
  1. Navigate to something like http://localhost/Sharepoint-to-MediaWiki/get-images.php
  2. Web page with AJAX requests to "get-image.php"
  3. Again, could not use command line PHP with cURL
  4. If have to use this no-command-line workaround, should write this step into the previous browser-as-a-command-line step
  5. Save each image to "./output/4-images" directory
5. IN A LINUX VIRTUAL MACHINE, run 5-convert-to-wiki.py, which:
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
