<?php



$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer -- Imported Items');
head($head);
//print_r($import);
//print_r($items);

?>


<h1>Imported Items from Feed: <?php echo $feed->feed_title; ?></h1>
<p>Import date: <?php echo $import->created ?></p>

<div id="primary">


<?php set_items_for_loop($items); ?>
   <ul class="items-list">
   <?php while (loop_items()): ?>
    <li class="item">
           <h4><?php echo link_to_item(); ?></h4>
        <?php if($desc = item('Dublin Core', 'Description', array('snippet'=>150))): ?>
        <div class="item-description"><?php echo $desc; ?></div>
        <?php endif; ?>       
    </li>       
<?php endwhile; ?>



</div>


<?php foot(); ?>