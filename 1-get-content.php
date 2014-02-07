<!DOCTYPE html>
<html>
  <?php $title = "Sharepoint-to-MediaWiki";?>
  <head>
    <title><?php echo $title; ?></title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script type="text/javascript" src="lib/page-analyze.js"></script>
	<script>
		window.pageNum = 0;
		window.imageNum = 0;
		window.sharepointImages = {};
		window.debug = {};
		window.errors = {
			pages : [],
			images : []
		};
		window.badimages = [];
		
		function disp (text) {
			$("#current").html(text);
		}
		
		function reformatPagesArray (pages) {
			var out = [];
			for(var page in pages) {
				out.push({ page : page, url : pages[page] });
			}
			return out;
		}
				
		function getNextPage () {
			disp("Processing page #" + pageNum + ": " + pages[pageNum].page);
			$.getJSON(
				"lib/get-page.php",
				{ url : pages[pageNum].url, page : pages[pageNum].page },
				function (response) {
					writeLine(response.message);
					pageNum++;
					
					analyzeSharepointpage( response.pagetitle, response.pageHTML );
					
				}
			);
			
		}
		
		function analyzeSharepointpage ( pagetitle, pageHTML ) {

			window.debug.lastPageHTML = pageHTML;
		
			var page = spPageAnalyzer.execute(
				pageHTML,
				'/MOD/DX/DX22/MSSDOC/ROBOpedia/',
				'Wiki%20Pages/',
				'Wiki%20Pictures/'
			);
			
			if (page === false) {
				var errormsg = "Problem extracting content from " + pagetitle + ".";
				window.errors.pages.push(errormsg);
				writeLine(
					"<span style='font-weight:bold;color:red;'>" + errormsg +
					" Proceeding to next...</span>"
				);
				checkNextPage();
			}
				
			
			for (var i=0; i<page.images.length; i++) {
				window.sharepointImages[page.images[i]] = 0; // dummy value for always unique "array"
			}
						
			$.post(
				"lib/save-sharepoint-content.php",
				{	pagetitle : pagetitle,
					pagecontent : page.content },
				function(response) {
					writeLine(response.message);
					checkNextPage();
				},
				"json"
			);
			
		}
		
		function checkNextPage () {
			if (pages[pageNum]) {
				getNextPage();
			}
			else {
				writeLine("<span style='font-weight:bold;'>Page retrieval and cropping complete!</span>");
				disp("Page retrieval and cropping complete!");
				processImages();
			}
		}
		
		function writeLine ( msg ) {
			$("#container").prepend("<li>" + msg + "</li>");
		}
		
		function writeError ( msg, type ) {
			$("#container").prepend("<li style='font-weight:bold;color:red;'>" + msg + "</li>");
			if (type == "page")
				window.errors.pages.push(msg);
			else if (type == "image")
				window.errors.images.push(msg);
			else
				alert( "coding error: incorrect error type" ); // sloppy
		}
		
		function processImages () {			
			var imgs = [];
			for(var im in window.sharepointImages) {
				imgs[imgs.length] = im;
			}
		
			window.sharepointImages = imgs; // overwrite object with array (object used to avoid duplicates)
		
			var imageList = JSON.stringify(window.sharepointImages);
			$.post(
				"lib/imageRecord.php",
				{ imageList : imageList },
				function(response){
					if (response.message && response.success == true) {
						writeLine(response.message);
						getNextImage();
					}
					else if (response.message) {
						writeError(response.message, "image");
					}
					else {
						writeError("Image list not recorded on server", "image");
					}
				},
				"json"
			);
		
		}
		
		function getNextImage () {
			disp("Processing image #" + imageNum + ": " + sharepointImages[imageNum]);
			$.getJSON(
				"lib/get-image.php",
				{ url : sharepointImages[imageNum] },
				function (response) {
					var success = false;
					if ( response.message ) {
						if ( response.success ) {
							writeLine(response.message);
							success = true;
						}
						else {
							writeError(response.message, "image");
						}
					}
					// non-json received
					else {
						writeError("Problem processing image #" + imageNum + ": " + sharepointImages[imageNum], "image");
					}
					
					if ( ! success )
						window.badimages.push(sharepointImages[imageNum]);
					
					imageNum++;
				
					if (sharepointImages[imageNum]) {
						getNextImage();
					}
					else {
						disp("<span style='font-size:20px;color:green;font-weight:bold;'>Operations Complete!!!</span>");
						displayErrors();
						setTimeout(function(){ alert("Operations Complete");}, 100);
					}
				}
			);
			
		}
		
		function displayErrors() {
			
			if (errors.pages.length > 0) {
				var numErrors = errors.pages.length;
				var msg = "";	
				for(var i = 0; i < numErrors; i++) {
					msg += "<li>" + errors.pages[i] + "</li>";
				}
				writeLine("<span style='font-weight:bold;color:red'>" + 
					numErrors + " error(s) found processing pages.<ul>"
					+ msg + "</ul></span>");
			}

			if (errors.images.length > 0) {
				var numErrors = errors.images.length+1;
				var msg = "";	
				for(var i = 0; i < errors.images.length; i++) {
					msg += "<li>" + errors.images[i] + "</li>";
				}
				writeLine("<span style='font-weight:bold;color:red'>" + 
					numErrors + " error(s) found processing images.<ul>"
					+ msg + "</ul></span>");
			}
			
		}

		function extractJsonPageListFromHtml ( html ) {
			var out = [];
			$(html).find("a").each(function(index,elem){
				var el = $(elem);
				out.push({
					page : el.html()
						.replace(".aspx","") // trim .aspx of the end
						.replace("&nbsp;"," "), // yes, Sharepoint has pagenames with &nbsp; in them
					url : el.attr("href").replace(/ /g, "%20") // URL encode spaces

				});
			});
			return out;
		}
		
		
		$(document).ready(function(){
			disp("Fetching list of pages");
			// $.getJSON("usr/sp-pages.json",function(data){
				// disp("List of pages retrieved");
				// window.pages = reformatPagesArray(data);
				// getNextPage();
			// });
			
			$.getJSON(
				"lib/convert-pages-from-xls.php",
				{},
				function(response) {
					if (response.success) {
						disp("List of pages retrieved");
						writeLine(response.message);
						window.pages = extractJsonPageListFromHtml( response.pageHTML );
						getNextPage();
					}
					else {
						disp("Operations Aborted: Page retrieval failed");
						writeError(response.message, "page");
					}
				}
			);
			
		});
    </script>
	<style>
		#current {
			font-family: courier, monospace;
			font-size: 16px;
		}
	</style>
  </head>
  <body>
    <h1><?php echo $title; ?></h1>
	<div id="current"></div>
    <ul id="container"></ul>
  </body>
</html>