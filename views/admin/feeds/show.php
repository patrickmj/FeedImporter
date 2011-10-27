<?php

$head = array('body_class' => 'feed-importer primary',
              'title'      => 'Feed Importer -- new',
              'content_class' => 'horizontal-nav');
              
              
head($head);

$collection = get_collection_by_id($feedimporter_feed->collection_id);
$importTable = get_db()->getTable('FeedImporter_Import');
$lastImport = $importTable->getMostRecentImportForFeedId($feedimporter_feed->id);


?>


<div id="primary">

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
		<tr>
			<td><?php echo $feedimporter_feed->id ?></td>
			<td><?php echo $feedimporter_feed->feed_title ?></td>
			<td><?php echo $feedimporter_feed->feed_description ?></td>
			<td>
				<?php
					if($collection) {
						 echo link_to($collection, 'show', $collection->name);
					} else {
						echo "No collection set";
					}
				?>
			</td>
			<td><?php echo $feedimporter_feed->feedItemCount() ?></td>
			<td><?php echo $lastImport->created ; ?></td>
			<td><?php echo $lastImport->status; ?></td>
			<td><a class="fi_import" href="<?php echo uri('feeds/import/' . $feedimporter_feed->id) ?>">Do Import Now</a></td>
			<td><a class="edit" href="<?php echo uri('feeds/edit/' . $feedimporter_feed->id ) ?>">Edit</td>
			<td><a class="fi_history" href="<?php echo uri('feeds/history/' . $feedimporter_feed->id) ?>">View Import History</a></td>
		</tr>
	</tbody>
</table>

<?php foot() ?>
