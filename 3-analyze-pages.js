var	fs = require('fs'),
	system = require('system');
var basePath = 'C:/xampp/htdocs/curl/';
var savePath = basePath + 'wiki-html/';
var sourcePath = basePath + 'raw-html/';
var spServer = 'https://modspops.jsc.nasa.gov',
	spPath = '/MOD/DX/DX22/MSSDOC/ROBOpedia/',
	spPageDir = 'Wiki%20Pages/',
	spPictureDir = 'Wiki%20Pictures/';

	
var oriPageList = JSON.parse(fs.read(basePath + '/sp-pages.json'));
var pageList = [];
for (var p in oriPageList) {
	pageList.push({
		page : p,
		url : oriPageList[p]
	});
}

phantom.onError = function(msg, trace) {
    var msgStack = ['PHANTOM ERROR: ' + msg];
    if (trace && trace.length) {
        msgStack.push('TRACE:');
        trace.forEach(function(t) {
            msgStack.push(' -> ' + (t.file || t.sourceURL) + ': ' + t.line + (t.function ? ' (in function ' + t.function + ')' : ''));
        });
    }
    console.error(msgStack.join('\n'));
    phantom.exit(0);
};





var currentPageListObj = 0;

function convert ( pageListObj ) {
	
	var fname = pageListObj.page + '.html';
	var source = sourcePath + fname;
	
	if ( ! fs.exists(source) ) {
		console.log(fname + " does not exist");
		phantom.exit(0);
	}
	console.log("Starting on " + source);

	
	var page = require('webpage').create();

	/**
	 * From PhantomJS documentation:
	 * This callback is invoked when there is a JavaScript console. The callback may accept up to three arguments: 
	 * the string for the message, the line number, and the source identifier.
	 */
	page.onConsoleMessage = function (msg, line, source) {
		console.log('console> ' + msg);
	};

	/**
	 * From PhantomJS documentation:
	 * This callback is invoked when there is a JavaScript alert. The only argument passed to the callback is the string for the message.
	 */
	page.onAlert = function (msg) {
		console.log('alert!!> ' + msg);
	};
	
	page.open(source, function (status) {

		page.includeJs("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
			
			var wiki = page.evaluate(function(spServer, spPath, spPageDir, spPictureDir) {
					
				// check if:
				//	(1) has more than one child element
				//	(2) ! isDivExternalClass
				var getWikiContentWrapper = function( elem ) {
				
					if ( isWikiContentWrapper(elem) )
						return $(elem);
					else
						return getWikiContentWrapper( $(elem).children().first() );
								
				};
				
				
				/**
				 *  Determines if an element is the wiki content wrapper
				 *
				 *  Checks if:
				 *    (1) element is a div
				 *    (2) element has one child
				 *    (3) element has class starting with "ExternalClass"
				 *
				 *  If any of these are not the case then we're no longer in the long chain 
				 *  of nested divs and  the wiki content wrapper. Else return true.
				 **/
				var isWikiContentWrapper = function( elem ) {
				
					if ( ! $(elem).is("div") ) // not a div?
						return true;
						
					if ( $(elem).children().length > 1 ) // has more than one child?
						return true;
				
					var re = /^ExternalClass.+/; // match classes starting with "ExternalClass"
					var classList = $(elem).first().attr('class').split(/\s+/);
					for (var i = 0; i < classList.length; i++) {
						if ( re.test(classList[i]) )
							return false;
					}
					
					// didn't find "ExternalClass..."
					return true;
				
				};
			
				var wiki = {
					content : null,
					images : [] //array of image src's, without server (so /images/my_image.png, not http://ms.com/images/my_image.png)
				};
			
				var wrapper = getWikiContentWrapper( 
					$('#ctl00_PlaceHolderMain_WikiField').first().find(".ms-wikicontent").children().first()
				);
				
				$(wrapper).find("a").each(function(i,e){
					var href;
					
					// no href on an <a> tag? Skip it. Perhaps should flag it somehow
					if ( ! (href = $(e).attr('href')) )
						return;
					
					spPagePath = spPath + spPageDir;
					spPicturePath = spPath + spPictureDir;
					
					// spPath = '/MOD/DX/DX22/MSSDOC/ROBOpedia/',
					// spPageDir = 'Wiki%20Pages/',
					// spPictureDir = 'Wiki%20Pictures/';
					
					// is it a Sharepoint Wiki Page?
					if ( href.slice(0,spPagePath.length) === spPagePath )
						$(e).attr('href', '/INTERNAL-WIKI-LINK'+href );

						
					// is it link directly to an image on the Sharepoint Wiki (not sure if they do this in practice)
					if ( href.slice(0,spPicturePath.length) === spPicturePath )
						$(e).attr('href', '/INTERNAL-WIKI-FILE-LINK'+href );

					return;
				
				});
				
				wiki.content = $(wrapper).html();
				
				$(wrapper).find('img').each(function(i,e){
					wiki.images.push( $(e).attr('src') );
				});
				
				return wiki;
				
			}, spServer, spPath, spPageDir, spPictureDir);

			
			
			var fileSaved,
				imagesAdded;
			try {
				fs.write(savePath + fname, wiki.content, 'w');
				fileSaved = "file saved";
			} catch(e) {
				fileSaved = "file save FAILED";
			}
			
			try {
				var imagetext = '';
				for(var i=0; i<wiki.images.length; i++) {
					imagetext += wiki.images[i] + "\n";
				}
				fs.write(savePath + 'images.txt', imagetext, 'a');
				imagesAdded = "images added";
			} catch(e) {
				imagesAdded = "image addition FAILED";
			}
			
			console.log(fname + ": " + fileSaved + ", " + imagesAdded);
			
			currentPageListObj++;
			if (pageList[currentPageListObj]) {
				
				setTimeout(function(){
					convert( pageList[currentPageListObj] );
				}, 1);
				
			}
			else {
				phantom.exit(1);
			}
			page.close();
		});


	});

}

convert( pageList[0] );

/* 
	outer_wrapper always has a div just inside it with class="ms-wikicontent ms-rtestate-field"
	but PHP will have trouble pulling by classname (and who knows, maybe there are more of these...)
	
	inside "ms-wikicontent ms-rtestate-field" there are then any number of matryoshka-nested-divs
	with classes like "ExternalClass451D2535DFE24FA5958F0773BEA7F06A"

	Getting first child element for each one, and stopping when you get to the first div that 
	doesn't have a class that starts with "ExternalClass..."
		
		Or perhaps just check for "does this element have more than one child element? If so, return
		the contents of this element"
		
		And if you get to an element with no child elements, then return the contents of that element
	
	
*/
