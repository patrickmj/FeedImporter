<?php

/**
 * Admin view of Feeds. 
 * Gives overview of status and settings
 */
$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer');
head($head);

?>

<p class="add-button" id="add-feed">
	<a class="add">Add Feed at URL: 
		<input id="new-feed-url" type="text" size="60" />
	</a>
</p>
<br/><br/>
<h1 style="clear:both">Browse Feeds</h1>


<div id="primary">
<?php
print_r($debug);
//echo $debug;
?>
<?php echo flash(); ?>
	<table>
		<thead>
			<tr>
				<th>Id</th>
				<th>Feed Name</th>
				<th>Feed Description</th>
				<th>Collection</th> <!-- Link to the collection -->
				<th>Items Imported</th> <!--# of items, with link to list of items -->
				<th>Last Import</th>
				<th>Import Status</th> <!-- Show the status of the import -->
				<th>Import Now?</th>
				<th>Edit?</th> <!-- Link to the edit view -->				
			</tr>
		</thead>
		<tbody>
<?php
	foreach($feedimporterfeeds as $feed):
	
	set_current_collection(get_collection_by_id($feed->collection_id));
	//TODO: dig up how many items imported
	$feedTable = get_db()->getTable('FeedImporter');
	//$importCount = $feedTable->importCount();
	$importCount = "N/A";
	//TODO: dig up status -- figure out Process and ProcessDispatcher
?>		
	<tr>
		<td><?php echo $feed->id ?></td>
		<td><?php echo $feed->feed_title ?></td>
		<td><?php echo $feed->feed_description ?></td>
		<td><?php echo link_to_collection() ?></td>
		<td><?php echo $importCount ?></td>
		<td><?php echo $feed->last_import_time ?></td>		
		<td><?php echo "Import status goes here"; ?></td>
		<td>Link to fire Import goes here</td>
		<td><a class="edit" href="<?php echo uri('feeds/edit/' . $feed->id ) ?>">Edit</td>
	</tr>	
<?php endforeach; ?>		
		
		</tbody>
	</table>
</div>

<div id="check-feed-dialog">

</div>



<?php
foot();
?>