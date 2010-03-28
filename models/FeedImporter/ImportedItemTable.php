<?php

class FeedImporter_ImportedItemTable extends Omeka_Db_Table
{

	public function findBySP_ID($sp_id)
	{	
		$sel = $this->getSelect()->where("sp_id = ?", $sp_id);
		return $this->fetchObjects($sel);		
	}
	
	public function findByPermalink($permalink)
	{	
		$sel = $this->getSelect()->where("permalink = ?", $permalink);
		return $this->fetchObjects($sel);		
	}
	
	public function findByImportId($import_id)
	{
		$sel = $this->getSelect()->where("import_id = ?", $import_id);
		return $this->fetchObjects($sel);		
	}
	
	public function countItemsFromImportId($import_id) 
	{
		//there must be a more efficient way to do this!
		$items = $this->findByImportId($import_id);
		return count($items);
	}
}
?>
