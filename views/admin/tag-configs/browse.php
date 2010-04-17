
<?php

$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer -- Tag',
              );
              
              
head($head);
$db= get_db();
$priorityArray = array();
$i = 1;
while($i < count($tags) + 1) {
	$priorityArray[] = $i;
	$i++;
}
?>

<div id="primary">

<?php


echo $debug;
print_r(Zend_Registry::get('pagination'));
?>

<script type="text/javascript">

FI= {};

FI.showElSet = function(e) {	
	jQuery('.feed-importer-element-set', e.target.parentNode).hide();
	jQuery('#feed-importer-element-set-' + e.target.value, e.target.parentNode).show();
	
}


</script>

<style type="text/css">

div#content {
	width: 960px;
	
}

div.feed-importer-element-set {
	display: none;
	
}

select {
	width: 125px;
	
}

label {
	width: 100px;
	
}

th > div {
	
	width: 100px;
}



</style>

<div class="pagination"><?php echo pagination_links(); ?></div>

<form accept-charset="utf-8" method="post" >
<table id="tags"  cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col">
        	<div id="feed-importer-original-name">
        		<h3>Feed Tag</h3>
	        	<div class="feed-importer-js-action">
	        		<p>Help</p>
	        	</div>
        	</div>
        </th>
        <th scope="col">
        	<div id="feed-importer-skip">
        		<h3>Skip?</h3>
	        	<div class="feed-importer-js-action">
	        		<p>Help</p>
	        	</div>
        	</div>        
        </th>        
        <th scope="col">
        	<div id="feed-importer-pref-name">
        		<h3>Preferred Name</h3>
	        	<div class="feed-importer-js-action">
	        		<p>Help</p>
	        	</div>
        	</div>        
        </th>
        <th scope="col">
        	<div id="feed-importer-collection-id">
        		<h3>Push Tagged Items to Collection</h3>
	        	<div class="feed-importer-js-action">
	        		<p>Help</p>
	        	</div>
        	</div>              
        </th>
        <th scope="col">
        	<div id="feed-importer-item-type-id">
        		<h3>Push Tagged Items to Item Type</h3>
	        	<div class="feed-importer-js-action">
	        		<p>Help</p>
	        	</div>
        	</div>              
        </th>
        <th scope="col">
        	<div id="feed-importer-tags-map">
        		<h3>Map Tag to Omeka Tags</h3>
	        	<div class="feed-importer-js-action">
	        		<p>Help</p>
	        	</div>
        	</div>              
        </th>        
		<th scope="col">
        	<div id="feed-importer-elements-map">
        		<h3>Map Tag to Elements</h3>
	        	<div class="feed-importer-js-action">
	        		<p>Help</p>
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
	<td><?php echo text(array('name'=>"tc[$tag->id][pref_name]", 'size'=>'10' ), $tag->pref_name) ; ?></td>
	<td><?php echo select_collection(array('name'=>"tc[$tag->id][collection_id]"));
	
	
	echo select(array('name'=>"tc[$tag->id][collection_id_priority]"), $priorityArray, $tag->collection_id_priority,  "Priority");
	?>

	</td>
	<td>
	<?php echo select_item_type(array('name'=>"tc[$tag->id][item_type_id]")); 
	
	echo select(array('name'=>"tc[$tag->id][item_type_id_priority]"), $priorityArray, $tag->item_type_id_priority,  "Priority");
	?>
	
	</td>
	<td>
		<?php 
			$tagTable = $db->getTable('Tag'); 
			$o_tags = $tagTable->findBy(array('sort'=>'alpha', 'type'=>'Item') );				
			$tagPairs = array();
			foreach($o_tags as $o_tag) {
				$tagPairs[$o_tag->id] = $o_tag->name;
				release_object($o_tag);
			}

			echo select(array('multiple'=>'true', 'size'=>'5', 'name'=>"tc[$tag->id][tags_map]"), $tagPairs, unserialize($tag->tags_map)); 
		?>
	</td>
	<td id="feed-importer-elements-tag-<?php echo $tag->id ?>">
	<?php
	$elSets = $db->getTable('ElementSet')->findAll();
	//print_r($elSets);
	$elSetPairs = array();
	foreach($elSets as $elSet) {
		$elSetPairs[$elSet->id] = $elSet->name;
		release_object($elSet);
	}
	
	//TODO: sort out the different select boxes
	echo "<label>Element Set</label>" . select(array('onchange'=>'FI.showElSet(event)',  'class'=>'fi_elset_select'), $elSetPairs); 
	foreach($elSetPairs as $id=>$elSet) {	
		$elementsMap = unserialize($tag->elements_map);	
		echo  "<div class='feed-importer-element-set' id='feed-importer-element-set-$id'>" . select_element(array('name'=>"tc[$tag->id][elements_map][$elSet]", 'multiple'=>'true', 'size'=>'5'), $elementsMap[$elSet] , $elSet, array('element_set_name'=>$elSet)) . "</div>";		
	}
	
	?>
	
	
	</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>
<?php echo submit(array('value'=>'Submit', 'class'=>'submit')) ?>
</form>
</div>


<?php foot(); ?>

