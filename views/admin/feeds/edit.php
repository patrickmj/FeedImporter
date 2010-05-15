<?php

$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer -- new',
              'content_class' => 'vertical-nav');
              
              
head($head);
?>


<script type="text/javascript">

Event.observe(window,'load',function(){
$$('.tabs').each(function(tab_group){  
     new Control.Tabs(tab_group);  
 });
});

</script>



<?php include "form-tabs.php" ?>

<div id="primary">

<form method="post" enctype="multipart/form-data" id="item-form" action="">
<?php foreach ($tabs as $tabName => $tabContent): ?>
    <?php if (!empty($tabContent)): ?>
        <div id="<?php echo text_to_id(html_escape($tabName)); ?>-metadata">
        <fieldset class="set">
            <legend><?php echo html_escape($tabName); ?></legend>
            <?php echo $tabContent; ?>        
        </fieldset>
        </div>     
    <?php endif; ?>
<?php endforeach; ?>	
<?php echo submit(array('name'=>'submit', 'id'=>'save-changes', 'class'=>'submit submit-medium'), 'Save Changes'); ?>	

<p id="delete_item_link">
	<a class="delete" href="<?php echo uri('feeds/delete/') . $feedimporter_feed->id ?>">Delete This Feed</a>       
</p>
</form>





</div>


<?php foot() ?>