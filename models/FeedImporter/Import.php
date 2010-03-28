<?php




class FeedImporter_Import extends Omeka_Record
{
	public $id;
	public $feed_id;
	public $collection_id;
	public $status;
	public $sp_error;
	public $created;
	private $fi_feed;

	const STATUS_IN_PROGRESS_IMPORT = 'Import In Progress';
    const STATUS_COMPLETED_IMPORT = 'Completed Import';
    const STATUS_IN_PROGRESS_UNDO_IMPORT = 'Undo Import In Progress';
    const STATUS_COMPLETED_UNDO_IMPORT = 'Completed Undo Import';
    const STATUS_IMPORT_ERROR_INVALID_FILE_DOWNLOAD = 'Import Error: Invalid File Download';
	const STATUS_FEED_ERRORS = 'Feed Error';




	public function doImport()
	{
		$db = get_db();   
 		$hasError = false;
 		require_once(PLUGIN_DIR . "/FeedImporter/libraries/SimplePie/simplepie.inc");
 		$this->fi_feed = $db->getTable('FeedImporter_Feed')->find($this->feed_id);
 		$sp_feed = new SimplePie();
		$sp_feed->set_feed_url($this->fi_feed->feed_url);		 
		// Run SimplePie.
		$sp_feed->init();
		if ($sp_feed->error()) {
			$hasError = true;
			$this->status = STATUS_FEED_ERRORS;
			$this->sp_error = $sp_feed->error();
			$this->save();
			return false;
			
		}				
		
		if(! $hasError) {
			foreach ($sp_feed->get_items() as $sp_item) {
	
				if( $this->_needsImport($sp_item)) {
					
					$metadataArray = $this->_buildFeedItemMetadata($sp_item);
					$elementTextsArray = $this->_buildFeedItemElementTexts($sp_item); 
					$newOmekaItem = insert_item($metadataArray, $elementTextsArray);
					$newImportedItem = new FeedImporter_ImportedItem();
					$newImportedItem->item_id = $newOmekaItem->id;
					$newImportedItem->import_id = $this->id;
					$newImportedItem->sp_id = $sp_item->get_id(true); //md5 hashes the post
					$newImportedItem->permalink = $sp_item->get_permalink();					
					$newImportedItem->save();
					$this->_buildFeedItemItemTypeData($sp_item, $newOmekaItem);
					//$this->_doFileImportForItem($sp_item);
								
				}
			}				
		}
	
 		//$this->view->debug = $debug;
 		$this->status = STATUS_COMPLETED_IMPORT ;
 		$this->save();
 		return true; 	
	}

 	
 	public function _buildFeedItemMetadata($sp_item) 
 	{
 		//check settings against FeedImporterFeed settings for what to do with everything
 		$metadataArray = array();
 		$metadataArray['collection_id'] = $this->fi_feed->collection_id;
 		//TODO: make Omeka Document the default item type
 		$metadataArray['item_type_id'] = 1; // just temporary until I build the UI to change this
 		//$metadataArray['item_type_id'] = $this->fi_feed->item_type_id;
 		if($this->fi_feed->tags_as_tags) {
 			$tags = $item->get_tags();
 			$tagsString = "";
 			foreach($tags as $tag) {
 				$tagsString .= $tag . ",";
 			}
 			$metadataArray['tags'] = $tagsString;
 		}	
 		return $metadataArray;
 	}
 	public function _buildFeedItemElementTexts($sp_item)
 	{
 		//check again FeedImporterFeed settings for how to handle elements
 		//first pass at this plugin does basic, predictable stuff, all toward Dublin Core
 		//later passes will build the advanced mapping to any available Element Set
 		//super-advanced optionn will stuff the data in as RDFa someday
 		
 		$elementTextsArray = array('Dublin Core');
		$title = $sp_item->get_title();
		$elementTextsArray['Dublin Core']['Title'][] = array('text'=>$title, 'html'=>false); 
		if($feedimporterfeed->content_as_description) {			
			$desc = substr($sp_item->get_description() , 0 , $this->fi_feed->trim_length);
			$elementTextsArray['Dublin Core']['Description'][] = array('text'=>$desc, 'html'=>false); 
		}
		
		if($this->fi_feed->tags_as_subjects) {
			foreach($sp_item->get_tags() as $tag) {
				if($this->fi_feed->tags_linkback) {
					$elementTextsArray['Dublin Core']['Subject'][] = array('text'=>$tag, 'html'=>false);
				} else {
					// how does SimplePie report back tag info?
					$scheme = $tag->get_scheme();
					$tagHTML = "<a href='" . $scheme . $tag . "' target='_blank'>$tag</a>";					
					$elementTextsArray['Dublin Core']['Subject'][] = array('text'=>$tagHTML, 'html'=>true);
				}
			}
		}
		$related = "<a href='" . $sp_item->get_permalink() .  "' target='_blank'>"  . $sp_item->get_title() . "</a>";
		$elementTextsArray['Dublin Core']['Relation'][] = array('text'=>$related, 'html'=>true);
		$elementTextsArray['Dublin Core']['Source'][] = array('text'=>$related, 'html'=>true);
		return $elementTextsArray;		
 	}
 	
 	private function _needsImport($sp_item)
 	{
 		return true;
 		
 		if ($this->_isImported($sp_item)) {
 			return false;
 		}
 		
 		
 		// check if date is in period for import
 		
 		if($this->fi_feed->import_start_time < $item->get_date() && $item->get_date() < $this->fi_feed->import_end_time) {
 			return true;
 		} else {
 			return false;
 		}
 		
 		
 	}
 	
 	private function _isImported($sp_item)
 	{ 		
		$t = $db->getTable('FeedImporter_ImportedItem');
		$sp_id = $sp_item->get_id();
		$permalink = $sp_item->get_permalink();
		$results = $t->findBySP_ID($sp_id);
		if(count($results) != 0 ) {
			return true;
		}
		
		$results = $t->findByPermalink($permalink);
		if(count($results) != 0 ) {
			return true;
		}		
		
		return false;
 	}
 	
 	private function _doFileImportForItem($sp_item, $fi_item_id) 
 	{
 		//TODO: the global insert files function might be trickier than I want??
 		$media = $sp_item->get_media(); 		
 	} 	
 	
 	private function _buildFeedItemItemTypeData($sp_item, $o_item)
 	{
 		$elTable = get_db()->getTable('Element');
 		
 		
 		//TODO: need a field on Feed for item_type_content_element
 		//for now, pretend it's Text
 		
 		$contentElementName = 'Text';
 		$contentElement = $elTable->findByElementSetNameAndElementName('Item Type Metadata', $contentElementName);
 		$o_item->addTextForElement($contentElement, $sp_item->get_content(), true);
 		$o_item->saveElementTexts();

 	}

	private function _buildMediaFileMetaData($sp_item)
	{
		
	}



}
?>
