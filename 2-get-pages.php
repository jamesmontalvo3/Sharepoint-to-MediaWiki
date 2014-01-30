<!DOCTYPE html>
<html>
  <?php $title = "Sharepoint-to-MediaWiki";?>
  <head>
    <title><?php echo $title; ?></title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script>
		window.pageNum = 0;
		
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
					$("#container").prepend(response.message);
					
					pageNum++;
				
					if (pages[pageNum]) {
						getNextPage();
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