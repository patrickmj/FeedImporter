<?php
/*
 * Created on May 19, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class FeedImporter_AuthorConfig extends Omeka_Record
 {
 	public $id;
 	public $original_name;
 	public $pref_name;
 	public $uri;
 	public $collection_id;
 	public $feed_id;
 	public $created;
 	public $count;
 	public $omeka_tags_map;
 	
 	public function getOmekaTags()
 	{
 		$o_tags = unserialize($this->omeka_tags_map);
 		return implode(',', $o_tags);
 	}
 	
 	public function getName()
 	{
 		return $this->pref_name ? $this->pref_name : $this->original_name;
 		
 	}
 	
 	
 }
?>
