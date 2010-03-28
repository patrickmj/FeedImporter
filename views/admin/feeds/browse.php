<?php

/**
 * Admin view of Feeds. 
 * Gives overview of status and settings
 */
$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer');
head($head);

?>
<form action="feeds/add" method="get">
<p class="add-button" id="add-feed">
	<button type="submit" class="add-button"  >Add Feed at URL </button>
		<input id="new-feed-url" name="feed_url" type="text" size="60" />
	
</p>
</form>

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
				<th>History</th>				
			</tr>
		</thead>
		<tbody>
		
		
<?php if($feedimporter_feeds): ?>
		
<?php
	
	foreach($feedimporter_feeds as $feed):
	
	$collection = get_collection_by_id($feed->collection_id);
	//TODO: dig up how many items imported
	$importTable = get_db()->getTable('FeedImporter_Import');
	$lastImport = $importTable->getMostRecentImportForFeedId($feed->id);
	
	
	//$importCount = $feedTable->importCount();
	//$importCount = $lastImport->;
	//TODO: dig up status -- figure out Process and ProcessDispatcher
?>		
	<tr>
		<td><?php echo $feed->id ?></td>
		<td><?php echo $feed->feed_title ?></td>
		<td><?php echo $feed->feed_description ?></td>
		<td>
			<?php 
				if($collection) {
					 echo link_to($collection, 'show', $collection->name);
				} else {
					echo "No collection set";
				}
			?>
		</td>
		<td><?php echo $importCount ?></td>
		<td><?php echo $lastImport->created ; ?></td>		
		<td><?php echo $lastImport->status; ?></td>
		<td><a class="fi_import" href="<?php echo uri('feeds/import/' . $feed->id) ?>">Do Import Now</a></td>
		<td><a class="edit" href="<?php echo uri('feeds/edit/' . $feed->id ) ?>">Edit</td>
		<td><a class="fi_history" href="<?php echo uri('feeds/history/' . $feed->id) ?>">View Import History</a></td>
	</tr>	
<?php endforeach; ?>		
<?php endif; ?>		
		</tbody>
	</table>
</div>

<div id="check-feed-dialog">

</div>



<?php
foot();
?>