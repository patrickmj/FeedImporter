<?php


class FeedImporter_FeedTable extends Omeka_Db_Table
{
	

	
	public function feedItemCount($feedId)
	{
		
		    
		    $db = get_db();
            $select = new Omeka_Db_Select();
            $select->from(array('ii' => 'feed_importer_imported_items'),
                          "COUNT(DISTINCT(ii.id))");
            $select->join(array('i' => 'feed_importer_imports'), 'i.id = ii.import_id', array());
            $select->join(array('f' => 'feed_importer_feeds'), 'f.id = i.feed_id', array());
            
            $select->where('f.id = ?');
            //echo $select;
            $feedItemCount = $db->fetchOne($select, array($feedId));
            return $feedItemCount;
	}	
	
	
	
}
?>
