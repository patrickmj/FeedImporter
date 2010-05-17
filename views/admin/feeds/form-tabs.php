<?php
$tabs = array();
$db = get_db();
$basics =  "<div class='feed-importer-field'><label>Feed URL</label>" . text(array('name'=>'feed_url', 'value'=>$feedimporter_feed->feed_url), $feedimporter_feed->feed_url) . "</div>";
$basics .= "<div class='feed-importer-field'><label>Feed Title</label>" . text(array('name'=>'feed_title', 'value'=>$feedimporter_feed->feed_title), $feedimporter_feed->feed_title) . "</div>"; 
$basics .= "<div class='feed-importer-field'><label>Feed Description</label>" . textarea(array('name'=>'feed_description', 'value'=>$feedimporter_feed->feed_description, 'rows'=>'5', 'cols'=>'50'), $feedimporter_feed->feed_description) . "</div>";
$basics .= "<div class='feed-importer-field'><label>Collection for items in feed</label>" . select_collection(array('name'=>'collection_id', 'value'=>$feedimporter_feed->collection_id), $feedimporter_feed->collection_id);
$basics .= "<p>Or create new collection from feed info ". checkbox(array('name'=>'new_collection')) ;
$basics .= ajax_create_dialog(array('type'=>'Collection', 'target'=>'#collection_id'));
$basics .= "</div>";


$tabs['Basics'] = $basics;

//feed settings sets up start and end dates for importing from feed, and frequency with which to check the feed
$feedSettings = "<div class='feed-importer-field'><label>Import Start Date</label><input id='import-start-date' class='date' type='text' /></div>"; //jquery a date popup
$feedSettings .= "<div class='feed-importer-field'><label>Import End Date</label><input id='import-end-date' class='date'  type='text' /></div>"; //jquery a date popup

$updateFreqVals = array('0'=>"Manual Updates Only", '1800'=>'Half hour', '3600'=>'Hour', '86400'=>'Daily', '604800'=>'Weekly');

$feedSettings .= "<div class='feed-importer-field'><label>Update Frequency</label>" . select(array('name'=>'update_frequency'), $updateFreqVals, $feedimporter_feed->update_frequency ) . "</div>";

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

$json = "FI.itemTypeElements = " . json_encode($itemTypeElementsObj) ;

$itemTypeHandling = "<script type='text/javascript'> 	
		$json;			
		 </script>";
$itemTypeHandling .= "<div onchange='FI.switchItemTypeElements(event)' id='item-type' class='feed-importer-field'><label>Item Type for imported items</label>" . select(array('name'=>'item_type_id'), $itemTypesArray, $feedimporter_feed->item_type_id) . "</div>";
$itemTypeHandling .= "<div id='content-element' class='feed-importer-field'><label>Element for item content</label>" . select(array('name'=>'content_element_id'), array(), $feedimporter_feed->content_element_id ) .  "<p class='explanation'>Select Item Type above to update options.</p></div>";

$tabs['Item Type Handling'] = $itemTypeHandling;



//content handling sets up whether to bring in the entire content, item type to use, what to do with media, how to handle linking and permalinks
// also, whether to use trimmed content as dct:description

$contentHandling = "<div class='feed-importer-field'><label>Import Content</label>" . checkbox(array('name'=>'import_content'), $feedimporter_feed->import_content) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Import Media</label>" . checkbox(array('name'=>'import_media'),  $feedimporter_feed->import_media) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Link Back</label>" . checkbox(array('name'=>'items_linkback'),  $feedimporter_feed->items_linkback). "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Use Content as Description</label>" . checkbox(array('name'=>'content_as_description'),  $feedimporter_feed->content_as_description) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Trim Content to length</label>" . select(array('name'=>'trim_length'), array(0=>'Full', '100'=>100, '200'=>200, '300'=>300),  $feedimporter_feed->trim_length) . "</div>";

$tabs['Content Handling'] = $contentHandling;

//tag/category handling sets up what to do with subject elements and how to map them
//e.g., as dct:subject, as tag on item, how to handle the scheme, etc.

$tagHandling = "<div class='feed-importer-field'><label>Add tags as subjects</label>" . checkbox(array('name'=>'tags_as_subjects'),  $feedimporter_feed->tags_as_subjects) . "</div>";
$tagHandling .= "<div class='feed-importer-field'><label>Add tags as item tags</label>" . checkbox(array('name'=>'tags_as_tags'),  $feedimporter_feed->tags_as_tags)  . "</div>";
$tagHandling .= "<div class='feed-importer-field'><label>Link back to tags</label>" . checkbox(array('name'=>'tags_linkback'),  $feedimporter_feed->tags_linkback)  . "</div>";


if($feedimporter_feed->id) {

$tagHandling .= "<p><a target='_blank' href='" . uri(array('feed_id'=>$feedimporter_feed->id, 'action'=>'browse'), 'feed_importer_tag_config_action') . "'>Configure tags</a></p>
		<p class='explanation'>You can configure individual tags to override these general options, and to customize Item Type and Collection handling based on individual tags. Use this option
		only if you know your metadata well, and are comfortable predicting options based on the tags coming from the feed";
		
}	



$tabs['Tag/Category Handling'] = $tagHandling;

//authorship handling sets up maps between feed authors and users in Omeka
// links back to author uris (if available)

$authorshipHandling = "<div class='feed-importer-field'><label>Add author as creator</label>" . checkbox(array('name'=>'author_as_creator'),  $feedimporter_feed->author_as_creator)  . "</div>";
$authorshipHandling .= "<div class='feed-importer-field'><label>Map authors to Omeka users</label>" . checkbox(array('name'=>'map_authors'),  $feedimporter_feed->map_authors)  . "</div>";



//TODO: build mechanism for making author map



?>


<ul id="section-nav" class="navigation tabs">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): // Don't display tabs with no content. '?>
            <li><a href="#<?php echo text_to_id(html_escape($tabName));?>-metadata"><?php echo html_escape($tabName); ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>


<script type="text/javascript">
jQuery(document).ready(function() { jQuery(".date").datepicker();  });

</script>