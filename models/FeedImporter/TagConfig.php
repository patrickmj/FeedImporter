<?php




class FeedImporter_TagConfig extends Omeka_Record

{
	public $id;
	public $created;
	public $original_name;
	public $pref_name;
	public $scheme;
	public $feed_id;
	public $collection_id;
	public $collection_id_priority;
	public $item_type_id;
	public $item_type_id_priority;
	public $count;
	public $skip;
	public $tags_map;
	public $elements_map;
	
	
	
	
	
	public function getName()
	{
		return $this->pref_name ? $this->pref_name : $this->original_name;		
	}
	
	

	
	
	
	
}
?>
