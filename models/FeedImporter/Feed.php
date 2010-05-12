<?php




class FeedImporter_Feed extends Omeka_Record
{
	
	public $id;
	public $collection_id;
	public $feed_url;
	public $feed_title;
	public $feed_description;
	public $import_start_time;
	public $import_end_time;
	public $feed_display_name;
	public $feed_display_description;
	public $item_type_id;
	public $import_media;
	public $trim_length;
	public $update_frequency;
	public $import_content;
	public $content_as_description;
	public $tags_as_subjects;
	public $tags_as_tags;
	public $tags_linkback;
	public $author_as_creator;
	public $map_authors;
	public $authors_to_users_map;
	public $map_tags;
	public $tags_map;
	public $items_linkback;
	public $task_id;

//Many thanks to Will Riley for this method
	public function feedItemCount($feedId = false)
	{
		    $feedId = $feedId ? $feedId : $this->id;
		    
		    
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
