<?php
/*
 * Created on Mar 25, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class FeedImporter_ImportedItem extends Omeka_Record
 {
	public $id;
	public $feed_id;
    public $import_id;
    public $item_id;
    public $permalink;
    public $sp_id;
	
	public function getItemId()
	{
	    return $this->item_id;
	}
	
	public function getImportId() 
	{
	    return $this->import_id;
	}

	public function findByImportId($import_id)
	{
		
	}
	
	public function findByFeedId($feed_id)
	{
		
	}
 }
?>
