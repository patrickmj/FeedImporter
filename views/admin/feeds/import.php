<?php


$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer -- Import');
head($head);

?>


<br/><br/>
<h1>Importing <?php echo $feed->feed_title; ?></h1>


<div id="primary">
<p>Started: <?php echo $import->created; ?></p>
<p>Status: <?php echo $import->status; ?></p>
<p>Go back to the Feeds page the check status</p>
</div>


<?php foot(); ?>