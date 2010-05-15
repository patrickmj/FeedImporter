<?php


$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer -- Import');
head($head);

?>


<br/><br/>
<h1>Import<?php echo $feed->feed_title; ?></h1>


<div id="primary">
<p>Started: <?php echo $feedimporter_import->created; ?></p>
<p>Status: <?php echo $feedimporter_import->status; ?></p>
<p>Reload to check status</p>
</div>

<script type="text/javascript">
function myCB(data) {
	
	alert(data.target);
}


</script>

<?php foot(); ?>