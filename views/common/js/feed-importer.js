var FI = {

	showElSet : function(e) {	
		jQuery('.feed-importer-element-set', e.target.parentNode).hide();
		jQuery('.feed-importer-element-set-' + e.target.value, e.target.parentNode).show();
	},

	showHelpText : function(topic) {
		alert(FI.helpTexts[topic]);
	},

	appendCollection: function(data) {
		newOption = FI.createOption(data);
		
		jQuery('.feed-importer-collection').append(newOption);
	},

	createOption: function(data) {
		newOption = document.createElement('option');
		jQuery(newOption).attr('value', data.id);
		jQuery(newOption).text(data.name);
		jQuery(newOption).attr('label', data.name);
		return newOption;
	},

	helpTexts : {
		origName : 'This is the original tag name coming from the feed. Note that feeds do not distinguish between a tag and a category',
		skip: 'Check this if you want to do absolutely nothing with this particular tag.',
		prefName: 'The preferred name is what will be use in all storage and display, including the maps to elements and tags.',
		collection: 'Override the default collection for a particular tag.',
		itemType: 'Override the default collection for this tag. For example, if you know the feed tags all videos with "Video"',
		tag: 'Map this tag to any number of Omeka item tags. For example, the original tag name "Paris" might map onto Omeka item tags "Paris" and "France". Or, it might map onto "Celebrities", depending on the nature of your site.',
		elSet: 'Map this tag onto any number of Omeka Elements. Select the Element Set you want to choose from, then select	the Elements.'
	},


		
	switchItemTypeElements : function(e) {		
		var elSel = document.getElementById('content_element_id');
		elSel.innerHTML = FI.itemTypeElements[e.target.value];
	}


};



