window.spPageAnalyzer = {
	
	// check if:
	//	(1) has more than one child element
	//	(2) ! isDivExternalClass
	getWikiContentWrapper : function( elem ) {
	
		if ( isWikiContentWrapper(elem) )
			return $(elem);
		else
			return getWikiContentWrapper( $(elem).children().first() );
					
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
	
	},

	/**
	 *  Parameter EXAMPLES:
	 *  spPath = '/MOD/DX/DX22/MSSDOC/ROBOpedia/'
	 *  spPageDir = 'Wiki%20Pages/'
	 *  spPictureDir = 'Wiki%20Pictures/'
	 *
	 *  RETURNS: modified content and list of image URLs
	 **/
	execute : function(spPath, spPageDir, spPictureDir) {
		
		var wiki = {
			content : null,
			images : [] //array of image src's, without server (so /images/my_image.png, not http://ms.com/images/my_image.png)
		};

		var wrapper = getWikiContentWrapper( 
			$('#ctl00_PlaceHolderMain_WikiField').first().find(".ms-wikicontent").children().first()
		);
		
		
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
			
			spPagePath = spPath + spPageDir;
			spPicturePath = spPath + spPictureDir;
						
			// is it a Sharepoint Wiki Page?
			if ( href.slice(0,spPagePath.length) === spPagePath )
				$(e).attr('href', '/INTERNAL-WIKI-LINK'+href );

				
			// is it link directly to an image on the Sharepoint Wiki (not sure if they do this in practice)
			if ( href.slice(0,spPicturePath.length) === spPicturePath )
				$(e).attr('href', '/INTERNAL-WIKI-FILE-LINK'+href );

			return;
		
		});
		
		wiki.content = $(wrapper).html();
		
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