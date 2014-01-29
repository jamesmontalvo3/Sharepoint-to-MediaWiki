<!DOCTYPE html>
<html>
  <head>
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
			$.get(
				"lib/get-page.php",
				{ url : pages[pageNum].url, page : pages[pageNum].page },
				function (data) {
					$("#container").append(data);
					
					pageNum++;
				
					if (pages[pageNum]) {
						getNextPage();
					}
					else {
						alert("Operation Complete");
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
    <h1>Sharepoint Grabber</h1>
	<div id="current"></div>
    <ul id="container"></ul>
  </body>
</html>