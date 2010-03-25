<?php

$head = array('body_class' => 'feed-importer primary', 
              'title'      => 'Feed Importer -- new',
              'content_class' => 'vertical-nav');
              
              
head($head);
?>

<style type="text/css">
div.feed-importer-field {
	clear: both;
	
}

</style>

<script type="text/javascript">

Event.observe(window,'load',function(){
$$('.tabs').each(function(tab_group){  
     new Control.Tabs(tab_group);  
 });
});

</script>



<?php include "form-tabs.php" ?>

<div id="primary">
<?php echo flash() ?>


<form method="post" enctype="multipart/form-data" id="item-form" action="add">
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
</form>




</div>


<?php foot() ?>