/**
 *	This file is meant to be used by copying and pasting the code
 *  into the Javascript console in your browser. Navigate to the
 *  Sharepoint list of all wiki pages, and run this code. It will 
 *  give you a stringified JSON object that you can paste into 
 *  a file for later use. Sorry, it sucks that this is so manual
 *  but I haven't had time to automate this process yet.
 **/

(function(){
	var s=document.createElement("script");
	s.src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js";
	document.body.appendChild(s);
	
	var stealFromSharepointFn = function() {
		console.log( "attempt steal" );
		window.stealFromSharepoint = {};

		jQuery(".itx a").each(function(index,element){ 

			stealFromSharepoint[ jQuery(element).html() ] = "https://modspops.jsc.nasa.gov" + jQuery(element).attr("href").replace(/ /g, "%20");

		});

		window.stolen = JSON.stringify( stealFromSharepoint );
		
		// jQuery("#s4-mainarea").prepend(
			// "<div style='height:500px;'><textarea style='width:90%;height:90%;'>" + window.stolen + "</textarea></div>"
		// );
		
		// jQuery("body").html("<textarea style='width:90%;height:90%;'>" + window.stolen + "</textarea>");
		jQuery("body").html("<div style='font-family:monospace;'>" + window.stolen + "</div>");
	};
	
	console.log( "Please wait 2 seconds..." );
	setTimeout(stealFromSharepointFn, 1000);
	
})();