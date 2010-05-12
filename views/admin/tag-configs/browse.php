
<?php

$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer -- Tag',
              );
              
              
head($head);


?>

<div id="primary">

<?php

//print_r( Zend_Registry::get('pagination') );

?>


<div class="pagination"><?php echo pagination_links(array('partial_file'=>'pagination_control.php')); ?></div>

<form accept-charset="utf-8" method="post" >
<table id="tags"  cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col">
        	<div id="feed-importer-original-name">
        		<h3>Feed Tag</h3>
	        	<div class="feed-importer-js-action">
	        		<p class="feed-importer-help" onclick="FI.showHelpText('origName')">Help</p>
	        	</div>
        	</div>
        </th>
        <th scope="col">
        	<div id="feed-importer-skip">
        		<h3>Skip?</h3>
	        	<div class="feed-importer-js-action">
	        		<p class="feed-importer-help" onclick="FI.showHelpText('skip')">Help</p>
	        	</div>
        	</div>        
        </th>        
        <th scope="col">
        	<div id="feed-importer-pref-name">
        		<h3>Preferred Name</h3>
	        	<div class="feed-importer-js-action">
	        		<p class="feed-importer-help" onclick="FI.showHelpText('prefName')">Help</p>
	        	</div>
        	</div>        
        </th>
        <th scope="col">
        	<div id="feed-importer-collection-id">
        	
        		<h3>Push Tagged Items to Collection</h3>
        		<?php echo ajax_create_dialog(array('type'=>'Collection', 'target'=>'.feed-importer-collection', 'label'=>'New') ); ?>
	        	<div class="feed-importer-js-action">
	        		<p class="feed-importer-help" onclick="FI.showHelpText('collection')">Help</p>
	        		
	        	</div>
        	</div>              
        </th>
        <th scope="col">
        	<div id="feed-importer-item-type-id">
        		<h3>Push Tagged Items to Item Type</h3>
        		<?php echo ajax_create_dialog(array('type'=>'ItemType', 'target'=>'.feed-importer-item-type', 'label'=>'New') ); ?>
	        	<div class="feed-importer-js-action">
	        		<p class="feed-importer-help" onclick="FI.showHelpText('itemType')">Help</p>
	        	</div>
        	</div>              
        </th>
        <th scope="col">
        	<div id="feed-importer-tags-map">
        		<h3>Map Tag to Omeka Tags</h3>
        		<?php echo ajax_create_dialog(array('type'=>'Tag', 'skipDescription'=>true, 'target'=>'.feed-importer-tag', 'label'=>'New') ); ?>
	        	<div class="feed-importer-js-action">
	        		<p class="feed-importer-help" onclick="FI.showHelpText('tag')">Help</p>
	        	</div>
        	</div>              
        </th>        
		<th scope="col">
        	<div id="feed-importer-elements-map">
        		<h3>Map Tag to Elements</h3>
	        	<div class="feed-importer-js-action">
	        		<p class="feed-importer-help" onclick="FI.showHelpText('elSet')">Help</p>
	        	</div>
        	</div>              
        </th>

        </tr>
    </thead>
    <tbody>

<?php  foreach($tags as $tag):  ?>
<tr>
	<td><?php echo $tag->original_name; ?></td>
	
	<td>
		<?php echo checkbox(array('name'=>"tc[$tag->id][skip]"), $tag->skip); ?>
	</td>
	<td><?php echo text(array('name'=>"tc[$tag->id][pref_name]", 'size'=>'10' ), $tag->getName()) ; ?></td>
	<td><?php echo select_collection(array('class'=>'feed-importer-collection', 'name'=>"tc[$tag->id][collection_id]"), $tag->collection_id);
	
	
	echo select(array('name'=>"tc[$tag->id][collection_id_priority]"), $priorityArray, $tag->collection_id_priority,  "Priority");
	?>

	</td>
	<td>
	<?php echo select_item_type(array('class'=>'feed-importer-item-type', 'name'=>"tc[$tag->id][item_type_id]")); 
	
	echo select(array('name'=>"tc[$tag->id][item_type_id_priority]"), $priorityArray, $tag->item_type_id_priority,  "Priority");
	?>
	
	</td>
	<td>
		<?php 
			echo select(array('class'=>'feed-importer-tag', 'multiple'=>'true', 'size'=>'5', 'name'=>"tc[$tag->id][tags_map]"), $tagPairs, unserialize($tag->tags_map)); 
		?>
	</td>
	<td>
	<?php	
		echo "<label>Element Set</label>" . select(array('onchange'=>'FI.showElSet(event)',  'class'=>'fi_elset_select'), $elSetPairs); 
		foreach($elSetPairs as $id=>$elSet) {
			$elementsMap = unserialize($tag->elements_map);	
			echo  "<div class='feed-importer-element-set feed-importer-element-set-$id'>" . select_element(array('name'=>"tc[$tag->id][elements_map][$elSet]", 'multiple'=>'true', 'size'=>'5'), $elementsMap[$elSet] , $elSet, array('element_set_name'=>$elSet)) . "</div>";
		}		
	?>
	
	
	</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>
<?php echo submit(array('value'=>'Submit', 'class'=>'submit')) ?>
</form>

	<div class="pagination"><?php echo pagination_links(); ?></div>
</div>


<?php foot(); ?>

