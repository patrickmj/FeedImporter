<?php
$tabs = array();
$db = get_db();
$basics =  "<div class='feed-importer-field'><label>Feed URL</label>" . text(array('name'=>'feed_url', 'value'=>$feedimporter_feed->feed_url), $feedimporter_feed->feed_url) . "</div>";
$basics .= "<div class='feed-importer-field'><label>Feed Title</label>" . text(array('name'=>'feed_title', 'value'=>$feedimporter_feed->feed_title), $feedimporter_feed->feed_title) . "</div>"; 
$basics .= "<div class='feed-importer-field'><label>Feed Description</label>" . textarea(array('name'=>'feed_description', 'value'=>$feedimporter_feed->feed_description, 'rows'=>'5', 'cols'=>'50'), $feedimporter_feed->feed_description) . "</div>";
$basics .= "<div class='feed-importer-field'><label>Collection for items in feed</label>" . select_collection(array('name'=>'collection_id', 'value'=>$feedimporter_feed->collection_id), $feedimporter_feed->collection_id);
$basics .= "<p>Or create new collection from feed info ". checkbox(array('name'=>'new_collection')) . "</div>";



$tabs['Basics'] = $basics;

//feed settings sets up start and end dates for importing from feed, and frequency with which to check the feed
$feedSettings = "<div class='feed-importer-field'><label>Import Start Date</label><input id='import-start-date' type='text' /></div>"; //jquery a date popup
$feedSettings .= "<div class='feed-importer-field'><label>Import End Date</label><input id='import-end-date' type='text' /></div>"; //jquery a date popup

$updateFreqVals = array('30'=>'Half hour', '60'=>'Hour', '1440'=>'Daily');

$feedSettings .= "<div class='feed-importer-field'><label>Update Frequency</label>" . select(array('name'=>'update_frequency'), $updateFreqVals, $feedimporter_feed->update_frequency ) . "</div>";
/*
$feedSettings .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$feedSettings .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$feedSettings .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$feedSettings .= "<div class='feed-importer-field'><label></label>" . . "</div>";
*/
$tabs['Feed Settings'] = $feedSettings;


//itemTypeHandling tells what Item Type to use for imported items, and what Element to use if content is imported
//TODO: ajax to return the right options for element based on choice of item type

$itemTypeTable = $db->getTable('ItemType');
$itemTypesArray = $itemTypeTable->findPairsForSelectForm();

//Dig up all the elements for each item type and make into <option>s for javascript to switch around when user selects an item type
//put them into an object, then serialize it to JSON
$itemTypeElementsObj = new stdClass();
foreach($itemTypesArray as $id=>$name) {
	$optionsHTML = "";	
	$itemType = $itemTypeTable->find($id);
	foreach($itemType->Elements as $element) {		
		$optionsHTML .= "<option value='$element->id' label='$element->name' >$element->name</option";
		release_object($element);
	}
	$itemTypeElementsObj->$id = $optionsHTML;
	release_object($itemType);
}

$json = "FeedImporter.ui.itemTypeElements = " . json_encode($itemTypeElementsObj) ;

$itemTypeHandling = "<script type='text/javascript'> 
		var FeedImporter = {}; 
		FeedImporter.ui = {};		
		$json;		

		
		FeedImporter.ui.switchItemTypeElements = function(e) {
		//dunno why I can't get jQuery techniques going on here'
		var elSel = document.getElementById('content_element_id');
		elSel.innerHTML = FeedImporter.ui.itemTypeElements[e.target.value];
		//jQuery('#content_element_id').html(FeedImporter.ui.itemTypeElements[e.target.value]));
		
		
		
		
						
				
				
		}		
		 </script>";
$itemTypeHandling .= "<div onchange='FeedImporter.ui.switchItemTypeElements(event)' id='item-type' class='feed-importer-field'><label>Item Type for imported items</label>" . select(array('name'=>'item_type_id'), $itemTypesArray, $feedimporter_feed->item_type_id) . "</div>";
$itemTypeHandling .= "<div id='content-element' class='feed-importer-field'><label>Element for item content</label>" . select(array('name'=>'content_element_id'), array(), $feedimporter_feed->content_element_id ) .  "<p class='explanation'>Select Item Type above to update options.</p></div>";

$tabs['Item Type Handling'] = $itemTypeHandling;

/*
$itemTypeHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$itemTypeHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>"; 
*/

//content handling sets up whether to bring in the entire content, item type to use, what to do with media, how to handle linking and permalinks
// also, whether to use trimmed content as dct:description

$contentHandling = "<div class='feed-importer-field'><label>Import Content</label>" . checkbox(array('name'=>'import_content'), $feedimporter_feed->import_content) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Import Media</label>" . checkbox(array('name'=>'import_media'),  $feedimporter_feed->import_media) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Link Back</label>" . checkbox(array('name'=>'items_linkback'),  $feedimporter_feed->items_linkback). "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Use Content as Description</label>" . checkbox(array('name'=>'content_as_description'),  $feedimporter_feed->content_as_description) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Trim Content to length</label>" . select(array('name'=>'trim_length'), array(0=>'Full', '100'=>100, '200'=>200, '300'=>300),  $feedimporter_feed->trim_length) . "</div>";
/*
$contentHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
*/
$tabs['Content Handling'] = $contentHandling;

//tag/category handling sets up what to do with subject elements and how to map them
//e.g., as dct:subject, as tag on item, how to handle the scheme, etc.

$tagHandling = "<div class='feed-importer-field'><label>Add tags as subjects</label>" . checkbox(array('name'=>'tags_as_subjects'),  $feedimporter_feed->tags_as_subjects) . "</div>";
$tagHandling .= "<div class='feed-importer-field'><label>Add tags as item tags</label>" . checkbox(array('name'=>'tags_as_tags'),  $feedimporter_feed->tags_as_tags)  . "</div>";
$tagHandling .= "<div class='feed-importer-field'><label>Link back to tags</label>" . checkbox(array('name'=>'tags_linkback'),  $feedimporter_feed->tags_linkback)  . "</div>";


$tagHandling .= "<div class='feed-importer-field'><label>Use a tag map</label>" . checkbox(array('name'=>'map_tags'),  $feedimporter_feed->map_tags)  . "</div>";
//TODO: build a tag mapping mechanism

/*$tagHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$tagHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$tagHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$tagHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
*/


$tabs['Tag/Category Handling'] = $tagHandling;

//authorship handling sets up maps between feed authors and users in Omeka
// links back to author uris (if available)

$authorshipHandling = "<div class='feed-importer-field'><label>Add author as creator</label>" . checkbox(array('name'=>'author_as_creator'),  $feedimporter_feed->author_as_creator)  . "</div>";
$authorshipHandling .= "<div class='feed-importer-field'><label>Map authors to Omeka users</label>" . checkbox(array('name'=>'map_authors'),  $feedimporter_feed->map_authors)  . "</div>";
//TODO: build mechanism for making author map
/*
$authorshipHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$authorshipHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$authorshipHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$authorshipHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$authorshipHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$authorshipHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$authorshipHandling .= "<div class='feed-importer-field'><label></label>" . . "</div>";
*/
$tabs['Authorship Handling'] = $authorshipHandling;

$advancedOptions = "<div><p>Advanced options override any of the usual options set elsewhere. Use this tab only if you really know what you're doing with metadata both from RSS/ATOM feeds and within the Element Sets you have withing your Omeka installation.</p></div> "; 

$advancedOptions .=  "<div><p>Maybe this only needs to show up if more than DC Element Set is present?</p></div>";

$tabs['Advanced Metadata Handling'] = $advancedOptions;


?>
<ul id="section-nav" class="navigation tabs">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): // Don't display tabs with no content. '?>
            <li><a href="#<?php echo text_to_id(html_escape($tabName));?>-metadata"><?php echo html_escape($tabName); ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>