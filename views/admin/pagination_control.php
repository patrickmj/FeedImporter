<?php if ($this->pageCount > 1): ?>
<?php $feed_id =  __v()->feed_id ;?>
<ul class="pagination_list">
    
    <?php if ($this->first != $this->current): ?>
    <!-- First page link --> 
    <li class="pagination_first">
    <a href="<?php echo html_escape($this->url(array('feed_id'=>$feed_id, 'page' => $this->first), 'feed_importer_tag_config_action_pages', $_GET)); ?>">First</a>
    </li>
    <?php endif; ?>
    
    <?php if (isset($this->previous)): ?>
    <!-- Previous page link --> 
    <li class="pagination_previous">
    <a href="<?php echo html_escape($this->url(array('feed_id'=>$feed_id, 'page' => $this->previous), 'feed_importer_tag_config_action_pages', $_GET)); ?>">Previous</a>
    </li>
    <?php endif; ?>
    
    <!-- Numbered page links -->
    <?php foreach ($this->pagesInRange as $page): ?> 
    <?php if ($page != $this->current): ?>
    
    
    
    <li class="pagination_range"><a href="<?php echo html_escape($this->url(array('feed_id'=>$feed_id, 'page' => $page), 'feed_importer_tag_config_action_pages', $_GET)); ?>"><?php echo $page; ?></a></li>
    <?php else: ?>
    <li class="pagination_current"><?php echo $page; ?></li>
    <?php endif; ?>
    <?php endforeach; ?>
    
    <?php if (isset($this->next)): ?> 
    <!-- Next page link -->
    <li class="pagination_next">
    <a href="<?php echo html_escape($this->url(array('feed_id'=>$feed_id, 'page' => $this->next), 'feed_importer_tag_config_action_pages', $_GET)); ?>">Next</a>
    </li>
    <?php endif; ?>
    
    <?php if ($this->last != $this->current): ?>
    <!-- Last page link --> 
    <li class="pagination_last">
    <a href="<?php echo html_escape($this->url(array('feed_id'=>$feed_id, 'page' => $this->last), 'feed_importer_tag_config_action_pages', $_GET)); ?>">Last</a>
    </li>
    <?php endif; ?>
</ul>
<?php endif; ?>
