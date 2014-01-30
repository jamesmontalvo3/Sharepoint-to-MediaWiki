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
			
			var page = spPageAnalyzer.execute(
				pageHTML,
				'/MOD/DX/DX22/MSSDOC/ROBOpedia/',
				'Wiki%20Pages/',
				'Wiki%20Pictures/'
			);
			
			for (var i=0; i<page.images.length; i++) {
				window.sharepointImages[page.images[i]] = 0; // dummy value for always unique "array"
			}
			
			$.post(
				"lib/save-sharepoint-content.php",
				{	pagetitle : pagetitle,
					pagecontent : page.content },
				function(response) {
					writeLine(response.message);
				
					if (pages[pageNum]) {
						getNextPage();
					}
					else {
						writeLine("<span style='font-weight:bold;'>Page retrieval and cropping complete!</span>");
						disp("Page retrieval and cropping complete!");
						processImages();
					}
				},
				"json"
			);
			
		}
		
		function writeLine ( msg ) {
			$("#container").prepend("<li>" + msg + "</li>");
		}
		
		function processImages () {			
			var imgs = [];
			for(var im in window.sharepointImages) {
				imgs[imgs.length] = im;
			}
		
			window.sharepointImages = imgs; // overwrite object with array (object used to avoid duplicates)
		
			getNextImage();
		}
		
		function getNextImage () {
			disp("Processing image #" + imageNum + ": " + sharepointImages[imageNum]);
			$.getJSON(
				"lib/get-image.php",
				{ url : sharepointImages[imageNum] },
				function (response) {
					writeLine(response.message);
					
					imageNum++;
				
					if (sharepointImages[imageNum]) {
						getNextImage();
					}
					else {
						disp("<span style='font-size:20px;color:green;font-weight:bold;'>Operations Complete!!!</span>");
						setTimeout(function(){ alert("Operations Complete");}, 100);
					}
				}
			);
			
		}

		
		$(document).ready(function(){
			disp("Fetching list of pages");
			$.getJSON("usr/sp-pages.json",function(data){
				disp("List of pages retrieved");
				window.pages = reformatPagesArray(data);
				getNextPage();
			});
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