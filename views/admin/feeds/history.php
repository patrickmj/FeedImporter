<?php

/**
 * Admin view of Feeds. 
 * Gives overview of status and settings
 */
$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer -- History');
head($head);
$importedItemTable = get_db()->getTable('FeedImporter_ImportedItem');
?>

<h1>Import History for <?php echo $feed->feed_title?></h1>
<p><a href="<?php echo uri('feeds/show/' . $feed->id); ?>">Feed View</a></p>
<?php echo flash(); ?>
<div id="primary">

<table>
	<tr>
		<th>Import Date</th>
		<th>Import Status</th>
		<th>Items Imported</th>
		<th>Undo?</th>
		<th>Browse Items Imported?</th>
	</tr>
	
	
<?php foreach($imports as $import): 
$itemsCount = $importedItemTable->countItemsFromImportId($import->id);

?>
	<tr>
		<td><?php echo $import->created; ?></td>
		<td><?php 
		
			if($import->status != "STATUS_FEED_ERRORS") {
				echo $import->status;
			} else {
				echo "<span class='fi_feed-error' title='$import->sp_error'>$import->status</span>";
			}
		
			?>
		</td>
		<td><?php echo $itemsCount  ?></td>
		<td><a class="fi_undo-import" href="<?php echo uri('feeds/imports/undoImport/' . $import->id); ?>">Undo?</a></td>
		<td><a href="<?php echo uri('feeds/imports/items/' . $import->id) ; ?>">Browse Items Imported</a></td>
	</tr>
<?php endforeach; ?>	
	
	
</table>


</div>



<?php foot(); ?>