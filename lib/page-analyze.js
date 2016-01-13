window.spPageAnalyzer = {

	getWikiContentWrapper : function (spPage) {

		// if the user has provided a specific selector to grab
		// sharepoint content from, use that instead of the method
		// below for determining the start of sharepoint content
		if ( window.sp2mwSettings.userDefinedWrapperSelector ) {
			return $(spPage).find( window.sp2mwSettings.userDefinedWrapperSelector );
		}

		outerWrapper = $(spPage).find( ".ms-wikicontent" );

		window.debug.outerWrapper = outerWrapper;
		if (outerWrapper.size() == 0)
			return false;
		else {
			if (outerWrapper.size() > 1) {
				writeLine("more than one .ms-wikicontent found");
				var outerWrapper = this.determineCorrectMsWikicontent( outerWrapper );
				if (outerWrapper === false)
					return false;
			}

			return this.getWikiContentWrapperHelper(
				$(outerWrapper).children().first()
			);
		}
	},

	determineCorrectMsWikicontent : function ( elems ) {

		if (elems.size() > 2) {
			writeLine("<span style='font-weight:bold;color:red;'>Currently cannot handle more than 2 .ms-wikicontent wrappers</span>");
			window.errors.pages.push("In unknown page: Currently cannot handle more than 2 .ms-wikicontent wrappers");
			return false;
		}
		else {
			if ( $.contains(elems[0], elems[1] ) )
				return elems[1];
			else if ( $.contains(elems[1], elems[0] ) )
				return elems[0];
			else
				return false; // two separate wrappers, no way to determine which to use
		}

	},

	// check if:
	//	(1) has more than one child element
	//	(2) ! isDivExternalClass
	getWikiContentWrapperHelper : function( elem ) {
		if ( this.isWikiContentWrapper(elem) )
			return $(elem);
		else
			return this.getWikiContentWrapperHelper( $(elem).children().first() );
	},


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
	isWikiContentWrapper : function( elem ) {

		if ( ! $(elem).is("div") ) // not a div?
			return true;

		// Has more than one child?: None of them can be another wrapper
		// Has no children?: Found content
		var numChildren = $(elem).children().length;
		if ( numChildren > 1 || numChildren == 0 )
			return true;

		var classes = $(elem).first().attr('class');

		// if this single wrapping element has any CSS classes, check to see
		// if any of them start with "ExternalClass". If any do, this element
		// is not part of the content, and is another wrapper.
		if (classes) {
			var re = /^ExternalClass.+/; // match starts with "ExternalClass"
			var classList = classes.split(/\s+/);
			for (var i = 0; i < classList.length; i++) {
				if ( re.test(classList[i]) )
					return false;
			}
		}

		// didn't find "ExternalClass..."
		return true;

	},

	/**
	 *  RETURNS: modified content and list of image URLs
     **/
	execute : function(spPage) {

		var wiki = {
			content : null,
			images : [] //array of image src's, without server (so /images/my_image.png, not http://ms.com/images/my_image.png)
		};

		var wrapper = this.getWikiContentWrapper(spPage);

		if (wrapper === false)
			return false;

		window.testWrapper = wrapper;

		var internalWikiLinkPrefix = '/INTERNAL-WIKI-LINK';

		// Loop through each <a>, add marker to HREFs so they can be changed
		// to wiki links later
		// Example:
		//     WAS:     <a href="http://ms.com/wiki/My_Page.asp">My Page</a>
		//     BECOMES: [[My Page]].
		$(wrapper).find("a").each(function(i,e){
			var href;

			// no href on an <a> tag? Skip it. Perhaps should flag it somehow
			if ( ! (href = $(e).attr('href')) )
				return;

			wikiURL = window.wikiURL;
			wikiURLnoProtocolOrHost = window.wikiURLnoProtocolOrHost;

			// is it a Sharepoint Wiki Page?
			if (   href.slice( 0, wikiURL.length                 ) === wikiURL
				|| href.slice( 0, wikiURLnoProtocolOrHost.length ) === wikiURLnoProtocolOrHost ) {
				$(e).attr('href', internalWikiLinkPrefix + href );
			}

			return;

		});

		// remove ZERO SPACE WIDTH character, a.k.a. ​&#8203; a.k.a. \u200B
		// replace &nbsp; with normal space
		wiki.content = $(wrapper).html()
			.replace(/\u200B/g,'')
			.replace("&nbsp;", ' ')
			.replace(/\u000D/g,'') // some control character
			.replace(/\u000A/g,'');

		// var decodeEntities = function ( encodedString ) {
		//     var textArea = document.createElement( 'textarea' );
		//     textArea.innerHTML = encodedString;
		//     return textArea.value;
		// }
		// wiki.content = decodeEntities( $(wrapper).html() );


		// Add all images in a page to an array of image URLs. These images
		// will all be downloaded later
		// There is no need to modify the HTML on the images, as the Perl
		// HTML-to-MediaWiki library will automatically change images into
		// the form [[File:Image file name.extension]].
		$(wrapper).find('img').each(function(i,e){
			wiki.images.push( $(e).attr('src') );
		});

		// return modified content and list of image URLs
		return wiki;
	}

};