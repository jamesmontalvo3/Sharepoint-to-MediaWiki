<!DOCTYPE html>
<html>
  <head>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script>
		window.imageNum = 0;
		
		function disp (text) {
			$("#current").html(text);
		}
				
		function getNextImage () {
			disp("Processing image #" + imageNum + ": " + images[imageNum]);
			$.get(
				"lib/get-image.php",
				{ url : images[imageNum] },
				function (data) {
					$("#container").append(data);
					
					imageNum++;
				
					if (images[imageNum]) {
						getNextImage();
					}
					else {
						alert("Operation Complete");
					}
				}
			);
			
		}
		
		$(document).ready(function(){
			disp("Fetching list of images");
			$.get("output/4-images.txt",function(data){
				disp("List of pages retrieved");
				window.images = data.split("\n");
				getNextImage();
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
    <h1>Sharepoint Image Grabber</h1>
	<div id="current"></div>
    <ul id="container"></ul>
  </body>
</html>