<?php
/**
 * 
 */
 
 class FeedImporter_FeedsController extends Omeka_Controller_Action
 {
 	
    public function init() 
    {
        $this->_modelClass = 'FeedImporterFeed';
    } 	
 	
	public function browseAction()
	{
		$debug = 'wtf';
		

		return parent::browseAction();
	}
 	
 	public function addAction()
 	{
 		require_once(PLUGIN_DIR . "/FeedImporter/libraries/SimplePie/simplepie.inc");
 		
 		$feed = new SimplePie();
		$feed->set_feed_url('http://simplepie.org/blog/feed/');		 
		// Run SimplePie.
		$feed->init();

 		//do some pre-processing 
 		//create a new collection if needed
 		if($_POST['new_collection']) {
 			$collectionID = $this->_createCollectionFromFeed($feed);
 		}
		parent::addAction();
 	}
 	
 	public function editAction()
 	{
 		parent::editAction();
 	}
 	
 	public function checkAction()
 	{
 		require_once(PLUGIN_DIR . "/FeedImporter/libraries/SimplePie/simplepie.inc");
 		
 		$feed = new SimplePie();
		$feed->set_feed_url('http://simplepie.org/blog/feed/');		 
		// Run SimplePie.
		$feed->init();
		$debug = "starting";
		
		foreach ($feed->get_items() as $item) {
		
			$debug .= "<br/>" . $item->get_id();
		}
		
 		$this->view->debug = $debug;
 	}
 	
 	public function importAction()
 	{
 		require_once(PLUGIN_DIR . "/FeedImporter/libraries/SimplePie/simplepie.inc");
 		
 		$feed = new SimplePie();
		$feed->set_feed_url($feedimporterfeed->feed_url);		 
		// Run SimplePie.
		$feed->init();
		$debug = "starting import";
		
		foreach ($feed->get_items() as $item) {
			//check whether it's aready imported
			//by an id? permalink? pub date compared to last import date?
			if(! $this->_checkNeedsImport($item)) {
				$metadataArray = $this->_buildFeedItemMetadata($item);
				$elementTextsArray = $this->_buildFeedItemElementTexts($item); 
				$newOmekaItem = insert_item($metadataArray, $elementTextsArray);
				$newImport = new FeedImporterImport();
				$newImport->item_id = $newOmekaItem->id;
				$newImport->feed_id = $feedimporterfeed->id;
				$newImport->collection_id = $feedimporterfeed->collection_id;
				$newImport->feed_item_id = $item->get_id();
				$newImport->feed_item_permalink = $item->get_permalink();
				$newImport->save();
				$debug .= "<br/>" . $item->get_title();				
			}
		}		
 		$this->view->debug = $debug; 		
 	}
 	
 	public function _buildFeedItemMetadata($item) 
 	{
 		//check settings against FeedImporterFeed settings for what to do with everything
 		$metadataArray = array();
 		$metadataArray['collection_id'] = $feedimporterfeed->collection_id;
 		$metadataArray['item_type_id'] = $feedimporterfeed->item_type_id;
 		if($feedimporterfeed->tags_as_tags) {
 			$tags = $item->get_tags();
 			$tagsString = "";
 			foreach($tags as $tag) {
 				$tagsString .= $tag . ",";
 			}
 			$metadataArray['tags'] = $tagsString;
 		}	
 		return $metadataArray;
 	}
 	public function _buildFeedItemElementTexts($item)
 	{
 		//check again FeedImporterFeed settings for how to handle elements
 		//first pass at this plugin does basic, predictable stuff, all toward Dublin Core
 		//later passes will build the advanced mapping to any available Element Set
 		//super-advanced optionn will stuff the data in as RDFa
 		
 		$elementTextsArray = array('Dublin Core');
		$title = $item->get_title();
		$elementTextsArray['Dublin Core']['Title'][] = array('text'=>$title, 'html'=>false); 
		if($feedimporterfeed->content_as_description) {			
			$desc = substr($item->get_description() , 0 , $feedimportfeed->trim_length);
			$elementTextsArray['Dublin Core']['Description'][] = array('text'=>$desc, 'html'=>false); 
		}
		
		if($feedimporterfeed->tags_as_subjects) {
			foreach($item->get_tags() as $tag) {
				if($feedimporterfeed->tags_linkback) {
					$elementTextsArray['Dublin Core']['Subject'][] = array('text'=>$tag, 'html'=>false);
				} else {
					// how does SimplePie report back tag info?
					$scheme = $tag->get_scheme();
					$tagHTML = "<a href='" . $scheme . $tag . "' target='_blank'>$tag</a>";					
					$elementTextsArray['Dublin Core']['Subject'][] = array('text'=>$tagHTML, 'html'=>true);
				}
			}
		}
		$related = "<a href='" . $item->get_permalink() .  "' target='_blank'>"  . $item->get_title() . "</a>";
		$elementTextsArray['Dublin Core']['Related'][] = array('text'=>$related, 'html'=>true);
		$elementTextsArray['Dublin Core']['Source'][] = array('text'=>$related, 'html'=>true);
		return $elementTextsArray;		
 	}
 	
 	public function _checkNeedsImport($item)
 	{
 		if ($this->_checkImported($item)) {
 			return false;
 		}
 		
 		
 		// check if date is in period for import
 		
 		if($feedimporterfeed->import_start_time < $item->get_date() && $item->get_date() < $feedimporterfeed->import_end_time) {
 			return true;
 		}
 	}
 	
 	public function _checkImported($item)
 	{
 		//check whether the item has already been imported. somehow.
 	}
 	
 	public function _buildFeedItemFiles($item) 
 	{
 		//TODO: the global insert files function might be trickier than I want??
 		$media = $item->get_media(); 		
 	} 	
 	
 	public function _createCollectionFromFeed($feed) {
 		$metadata = array();
 		$metadata['name'] = $feed->get_title();
 		$metadata['description'] = $feed->get_description();
 		$newCollection = insert_collection($metadata);
 		$feedimporterfeed->collection_id = $newCollection->id;
 		return $newCollection->id;
 	}
 }
?>
