<?php




class FeedImporter_Import extends Omeka_Record
{
	public $id;
	public $feed_id;
	public $collection_id;
	public $status;
	public $sp_error;
	public $created;
	


	const STATUS_IN_PROGRESS_IMPORT = 'Import In Progress';
    const STATUS_COMPLETED_IMPORT = 'Completed Import';
    const STATUS_IN_PROGRESS_UNDO_IMPORT = 'Undo Import In Progress';
    const STATUS_COMPLETED_UNDO_IMPORT = 'Completed Undo Import';
    const STATUS_IMPORT_ERROR_INVALID_FILE_DOWNLOAD = 'Import Error: Invalid File Download';
	const STATUS_FEED_ERRORS = 'Feed Error';


	public function getItems()
	{
		$iiTable = $this->getDb()->getTable('FeedImporter_ImportedItem');
		$itemTable = $this->getDb()->getTable('Item');
		$importedItems = $iiTable->findByImportId($this->id);
		$items = array();
		foreach($importedItems as $importedItem) {
			$items[] = $itemTable->find($importedItem->item_id);
			release_object($importedItem);
		}
		
		return $items;
	}

	public function undoImport()
	{
	    // first save the import object in the database
        
	    $this->status = self::STATUS_IN_PROGRESS_UNDO_IMPORT;
	    $this->save();
		$itemTable = $this->getDb()->getTable('Item');
		$this->status = "Got item table";
		$this->save();
		$importedItemTable = $this->getDb()->getTable('FeedImporter_ImportedItem');
		$this->status = "got imported item table";
		$this->save();
        $importedItems = $importedItemTable->findByImportId($this->id);
        $this->status = "got imported items";
        $this->save();
        while(count($importedItems) > 0) {
            foreach($importedItems as $importedItem) {
                
                $item = $itemTable->find($importedItem->item_id);
                if ($item) {
                    $item->delete();
                }
                $importedItem->delete();
            }
            $importedItems = $importedItemTable->findByImportId($this->id);
        }
        
        
        $this->delete();
		return true;
	}



	public function doImport()
	{
_log("Feed Import Started at "  . date('c') );
		$db = get_db();
 		$hasError = false;
 		require_once(PLUGIN_DIR . "/FeedImporter/libraries/SimplePie/simplepie.inc");
 		$this->fi_feed = $db->getTable('FeedImporter_Feed')->find($this->feed_id);
 		$this->fi_tags = $db->getTable('FeedImporter_TagConfig')->findByFeedId($this->feed_id, true);
 		
 		if($this->fi_feed->map_authors) {
 			$this->authorsMap = unserialize($this->fi_feed->authors_map);
 		}
 		 		
 		$sp_feed = new SimplePie();
 		$sp_feed->enable_cache(false);
		$sp_feed->set_feed_url($this->fi_feed->feed_url );
		$sp_feed->handle_content_type();
		// Run SimplePie.
		$sp_feed->init();
		
		if ($sp_feed->error()) {
			$hasError = true;
			$this->status = self::STATUS_FEED_ERRORS;
			$this->sp_error = $sp_feed->error();
			$this->save();
			return false;
		}
		

		if(! $hasError) {

			foreach ($sp_feed->get_items() as $sp_item) {
                
				if( $this->_needsImport($sp_item)) {
				    

					$this->processItemTags($sp_item);

					$metadataArray = $this->buildFeedItemMetadata($sp_item);

					$elementTextsArray = $this->buildFeedItemElementTexts($sp_item);

					$newOmekaItem = insert_item($metadataArray, $elementTextsArray);

					$newImportedItem = new FeedImporter_ImportedItem();

					$newImportedItem->item_id = $newOmekaItem->id;
					$newImportedItem->feed_id = $this->fi_feed->id;
					$newImportedItem->import_id = $this->id;
					$newImportedItem->sp_id = $sp_item->get_id();
			
					$newImportedItem->save();
					$this->_buildFeedItemItemTypeData($sp_item, $newOmekaItem);
				    $license = $sp_item->get_link(0, 'license');

					if($this->fi_feed->import_media && !empty($license)) {
						$this->_doFileImportForItem($sp_item, $newOmekaItem);
					}
				}
			}
		}

 		$this->status = self::STATUS_COMPLETED_IMPORT ;
 		$this->save();
 		return true;
	}

 	
 	public function buildFeedItemMetadata($sp_item)
 	{
 		//check settings against FeedImporterFeed settings for what to do with everything
 		$metadataArray = array();

 		$metadataArray['collection_id'] = $this->getCollectionId($sp_item);

 		//TODO: make Omeka Document the default item type
 		//$metadataArray['item_type_id'] = 1; // just temporary until I build the UI to change this

 		$metadataArray['item_type_id'] = $this->getItemTypeId($sp_item);
 		
 		//work with the tag settings

 		if($this->fi_feed->tags_as_tags) {
 			$metadataArray['tags'] = $this->getOmekaTags($sp_item);
 		}

 		$metadataArray['public'] = $this->isItemPublic($sp_item);
 		
 		return $metadataArray;
 	}
 	
 	public function buildFeedItemElementTexts($sp_item)
 	{
 		//check again FeedImporterFeed settings for how to handle elements
 		//first pass at this plugin does basic, predictable stuff, all toward Dublin Core
 		//later passes will build the advanced mapping to any available Element Set
 		
 		$elementTextsArray = array();
		$title = htmlspecialchars_decode($sp_item->get_title() );
		$permalink = $sp_item->get_permalink();
		if($this->fi_feed->items_linkback) {
			$titleHTML = "<a href='$permalink'>$title</a>";
			$elementTextsArray['Dublin Core']['Title'][] = array('text'=>$title, 'html'=>true);
		} else {
			$elementTextsArray['Dublin Core']['Title'][] = array('text'=>$title, 'html'=>false);
		}
		 
		if($this->fi_feed->content_as_description) {
			$desc = $sp_item->get_content();
			$elementTextsArray['Dublin Core']['Description'][] = array('text'=>$desc, 'html'=>true);
		}
		//build up tag-based metadata
		$elementTextsArray = $this->addElementTextsByTags($sp_item, $elementTextsArray);
		
		//build up standard bits of metadata
		$related = "<a href='" . $sp_item->get_permalink() .  "' target='_blank'>"  . $title . "</a>";
		$elementTextsArray['Dublin Core']['Relation'][] = array('text'=>$related, 'html'=>true);
		$elementTextsArray['Dublin Core']['Source'][] = array('text'=>$related, 'html'=>true);
		$elementTextsArray['Dublin Core']['Identifier'][] = array('text'=>$sp_item->get_id(), 'html'=>false);
		$elementTextsArray['Dublin Core']['License'][] = $this->processLicense($sp_item);
		
		//build up creator metadata
		//TODO: this should also be mapable to any element
		$elementTextsArray['Dublin Core']['Creator'] = array();
		if($this->fi_feed->author_as_creator) {
			$authors = $sp_item->get_authors();
			foreach($authors as $author) {
				if($this->fi_feed->map_authors) {
					$authorName = $this->_mapAuthorName($author->get_name());
				} else {
					$authorName = $author->get_name();
				}
				$authorLink = $author->get_link();
				$authorName = "<a href='$authorLink'>$authorName</a>";
				$elementTextsArray['Dublin Core']['Creator'][] = array('text'=>$authorName, 'html'=>true);
			}
		}
		return $elementTextsArray;
 	}

	public function addElementTextsByTags($sp_item, $elementTextsArray)
	{
		$linkBack = $this->fi_feed->tags_linkback;
		$tagNames = $this->_getItemTagNames($sp_item);
		
		foreach($tagNames as $tagName){
			if($this->fi_tags[$tagName]->skip) {
				continue;
			}
			if($this->fi_feed->tags_as_subjects) {
				$elementTextsArray['Dublin Core']['Subject'][] = $this->fi_tags[$tagName]->getElementText($linkBack);
			}
			if($this->fi_tags[$tagName]->elements_map) {
				$elMap = unserialize($this->fi_tags[$tagName]->elements_map);
				foreach($elMap as $elSet=>$elements) {
					$elMap[$elSet] = array_values($elMap[$elSet]);
				}
				
				//If the more general map to DC subjects is set, remove the duplicates
				if($this->fi_feed->tags_as_subjects) {
					unset($elMap['Dublin Core']['Subject']);
				}
				$elementTextsArray = array_merge($elementTextsArray, $elMap);
			}
		}
		return $elementTextsArray;
	}

	public function isItemPublic($sp_item)
	{
	    return true;
	}
//TODO: is this used anywhere?

	public function processFeedTags($sp_feed, $feed_id)
	{
		$this->feed_id = $feed_id;
		$this->fi_tags = get_db()->getTable('FeedImporter_TagConfig')->findByFeedId($feed_id, true);
		$sp_items = $sp_feed->get_items();
		foreach($sp_items as $sp_item) {
			$this->processItemTags($sp_item);
		}
	}

 	public function processItemTags($sp_item)
 	{
 		$tags = $sp_item->get_categories();
 		foreach($tags as $tag) {
 			$tagName = trim($tag->get_label());
 			if($this->fi_tags && array_key_exists($tagName, $this->fi_tags)) {
 				$this->fi_tags[$tagName]->count++;
 				$this->fi_tags[$tagName]->save();
 			} else {
 				$newTC = new FeedImporter_TagConfig;
 				$newTC->scheme = $tag->get_scheme();
 				$newTC->original_name = $tagName;
 				$newTC->count = 1;
 				$newTC->feed_id = $this->feed_id;
 				$newTC->created = date('Y-m-d G:i:s');
 				$this->fi_tags[$tagName] = $newTC;
 				$newTC->save();
 			}
 		}
 	}






 	public function processLicense($sp_item)
 	{
 	    $license = $sp_item->get_link(0, 'license');
 	    $elText = array('text'=> '', 'html'=>true);

 	    switch($license) {
 	        case 'http://creativecommons.org/licenses/by-nc-sa/2.0/deed.en':
 	            $elText['text'] = "<a href='$license'>CC BY-NC-SA</a>";
 	            break;
 	        case 'http://creativecommons.org/licenses/by/2.0/deed.en':
 	            $elText['text'] = "<a href='$license'>CC BY</a>";
 	            break;
 	        case 'http://creativecommons.org/licenses/by-nd/2.0/deed.en':
 	            $elText['text'] = "<a href='$license'>CC BY-ND</a>";
 	            break;
 	            
 	        case 'http://creativecommons.org/licenses/by-sa/2.0/deed.en':
 	            $elText['text'] = "<a href='$license'>CC BY-SA</a>";
 	            break;
 	            
 	        case 'http://creativecommons.org/licenses/by-nc/2.0/deed.en':
 	            $elText['text'] = "<a href='$license'>CC BY-NC</a>";
 	            break;
 	            
 	        case 'http://creativecommons.org/licenses/by-nc-nd/2.0/deed.en':
 	            $elText['text'] = "<a href='$license'>CC BY-NC-ND</a>";
 	            break;
            
 	        default:
 	            $elText['text'] = "All Rights Reserved";
 	            $elText['html'] = false;
 	            break;
 	    }
 	    return $elText;
 	}
 	public function getOmekaTags($sp_item)
 	{
 		$sp_tagNames = $this->_getItemTagNames($sp_item);

 		$o_tagsArray = array();
 		
 		foreach($sp_tagNames as $tagName) {

			$o_tagsArray[] = $this->fi_tags[$tagName]->getName();

 			
 			if($this->fi_tags[$tagName]->tags_map) {
 				$tagsMap = unserialize($this->fi_tags[$tagName]->tags_map);
 				$o_tagsArray = array_merge($o_tagsArray, array_values($tagsMap) );
 			}
 		}
 		return implode(',', $o_tagsArray);
 	}
 	
 	public function getCollectionId($sp_item)
 	{

 		$tagNames = $this->_getItemTagNames($sp_item);

 		$currPriority = -1;
 		foreach($tagNames as $tagName) {
 			if(array_key_exists($tagName, $this->fi_tags)
 				&& $this->fi_tags[$tagName]->collection_id
 				&& ($this->fi_tags[$tagName]->collection_id_priority > $currPriority)
 			
 				) {
 				$priority = $this->fi_tags[$tagName]->collection_id_priority;
 				$collectionId = $this->fi_tags[$tagName]->collection_id;
 			}
 		}
 		if(! $collectionId ) {
 			$collectionId = $this->fi_feed->collection_id;
 		}
 		return $collectionId;
 	}
 	
 	public function getItemTypeId($sp_item)
 	{
        //for occupy archive flickr importer only
        $enclosures = $sp_item->get_enclosures();
        foreach($enclosures as $enclosure) {
            $type = $enclosure->get_real_type();
            if($type == 'application/x-shockwave-flash') {
                return 3;
            }
        }
        return 6;

 		//$tagNames = $this->getItemTagNames($sp_item);
 		
 		$currPriority = -1;
 		foreach($tagNames as $tagName) {
 			if(array_key_exists($tagName, $this->fi_tags)
 				&& $this->fi_tags[$tagName]->item_type_id
 				&& ($this->fi_tags[$tagName]->item_type_id_priority > $currPriority)
 				) {
 				$priority = $this->fi_tags[$tagName]->item_type_id_priority;
 				$itemTypeId = $this->fi_tags[$tagName]->item_type_id_priority;
 			}
 		}
 		if( ! $itemTypeId ) {
 			$itemTypeId = $this->fi_feed->item_type_id;
 		}

 		return $itemTypeId;
 	}
 	
 	private function _getItemTagNames($sp_item)
 	{
 		$tagNames = array();
 		$itemTags = $sp_item->get_categories();
 		foreach($itemTags as $tag) {
 			$tagNames[] = trim($tag->get_label());
 		}
 		return $tagNames;
 	}
 	
 	private function _needsImport($sp_item)
 	{
 		
 		if ($this->_isImported($sp_item)) {
 			return false;
 		}

 		// check if date is in period for import
 		/*
 		if( $this->fi_feed->import_start_time < $item->get_date() && $item->get_date() < $this->fi_feed->import_end_time) {
 			return true;
 		}
 		*/

 		return true;
 	}
 	
 	private function _isImported($sp_item)
 	{
 		$db = get_db();
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
 	
 	private function _doFileImportForItem($sp_item, $o_item)
 	{
 		$enclosures = $sp_item->get_enclosures();
		$filesArray = array();
		foreach($enclosures as $enclosure) {
			$enclosureInfoArray = array();
			$enclosureInfoArray['source'] = $enclosure->get_link();
			$enclousreInfoArray['name'] = $enclosure->get_title();
			$enclosureInfoArray['metadata'] = $this->_buildMediaFileMetaData($enclosure);
			$filesArray[] = $enclosureInfoArray;
		}
		try {
			insert_files_for_item($o_item, 'UrlClio', $filesArray);
		} catch (Exception $e) {
			echo "insert fail";
		}
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

	private function _buildMediaFileMetaData($enclosure)
	{
 		$elementTextsArray = array('Dublin Core'=>array());
		$elementTextsArray['Dublin Core']['Title'][] = array('text'=>$enclosure->get_title(), 'html'=>false);
		$elementTextsArray['Dublin Core']['Type'][] = array('text'=>$enclosure->get_type(), 'html'=>false);
		foreach((array) $enclosure->get_categories() as $tag) {
				$elementTextsArray['Dublin Core']['Subject'][] = array('text'=>$tag, 'html'=>false);
			
		}
		
		return $elementTextsArray;
	}


	
}
?>
