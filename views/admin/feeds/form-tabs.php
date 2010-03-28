<?php
$tabs = array();

$basics =  "<div class='feed-importer-field'><label>Feed URL</label>" . text(array('name'=>'feed_url', 'value'=>$feedimporter_feed->feed_url), $feedimporter_feed->feed_url) . "</div>";
$basics .= "<div class='feed-importer-field'><label>Feed Title</label>" . text(array('name'=>'feed_title', 'value'=>$feedimporter_feed->feed_title), $feedimporter_feed->feed_title) . "</div>"; 
$basics .= "<div class='feed-importer-field'><label>Feed Description</label>" . textarea(array('name'=>'feed_description', 'value'=>$feedimporter_feed->feed_description, 'rows'=>'5', 'cols'=>'50'), $feedimporter_feed->feed_description) . "</div>";
$basics .= "<div class='feed-importer-field'><label>Collection for items in feed</label>" . select_collection(array('name'=>'collection_id', 'value'=>$feedimporter_feed->collection_id), $feedimporter_feed->collection_id);
$basics .= "<p>Or create new collection from feed info ". checkbox(array('name'=>'new_collection')) . "</div>";



$tabs['Basics'] = $basics;

//feed settings sets up start and end dates for importing from feed, and frequency with which to check the feed
$feedSettings = "<div class='feed-importer-field'><label>Import Start Date</label>" . text(array()) . "</div>"; //jquery a date popup
$feedSettings .= "<div class='feed-importer-field'><label>Import End Date</label>" . text(array()) . "</div>"; //jquery a date popup
$feedSettings .= "<div class='feed-importer-field'><label>Update Frequency</label>" . select(array('name'=>'update_frequency')) . "</div>";
/*
$feedSettings .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$feedSettings .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$feedSettings .= "<div class='feed-importer-field'><label></label>" . . "</div>";
$feedSettings .= "<div class='feed-importer-field'><label></label>" . . "</div>";
*/
$tabs['Feed Settings'] = $feedSsttings;

//content handling sets up whether to bring in the entire content, item type to use, what to do with media, how to handle linking and permalinks
// also, whether to use trimmed content as dct:description

$contentHandling = "<div class='feed-importer-field'><label>Import Content</label>" . checkbox(array('name'=>'import_content'), $feedimporter_feed->import_content) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Import Media</label>" . checkbox(array('name'=>'import_media'),  $feedimporter_feed->import_media) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Link Back</label>" . checkbox(array('name'=>'items_linkback'),  $feedimporter_feed->items_linkback). "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Use Content as Description</label>" . checkbox(array('name'=>'content_as_description'),  $feedimporter_feed->content_as_description) . "</div>";
$contentHandling .= "<div class='feed-importer-field'><label>Trim Content to length</label>" . select(array('name'=>'trim_length'),  $feedimporter_feed->trim_length) . "</div>";
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

?>
<ul id="section-nav" class="navigation tabs">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): // Don't display tabs with no content. '?>
            <li><a href="#<?php echo text_to_id(html_escape($tabName));?>-metadata"><?php echo html_escape($tabName); ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>