<?php
/**
 * 
 */
 
 class FeedImporter_FeedsController extends Omeka_Controller_Action
 {
 	
    public function init() 
    {
        $this->_modelClass = 'FeedImporter_Feed';
    } 	
 	
	public function browseAction()
	{
		// need to dig up related import info for each feed
		

		return parent::browseAction();
	}
 	
 	public function addAction()
 	{
 		require_once(PLUGIN_DIR . "/FeedImporter/libraries/SimplePie/simplepie.inc");

/*
array(
      'name'       => [string], 
      'description'=> [string], 
      'public'     => [true|false], 
      'featured'   => [true|false]
      'collectors' => [array of entities, entity ids, or entity property arrays]
  )
*/
   		
 		$feed = new SimplePie();
 		
        $varName = strtolower($this->_modelClass);
        $class = $this->_modelClass;
        
        $record = new FeedImporter_Feed();
 		//do some pre-processing 
 		//create a new collection if needed
 		
 		if($_GET['feed_url']) {
 			$feed_url = $_GET['feed_url'];
 			$debug = new stdClass();	
			$feed->set_feed_url($feed_url);		 
			// Run SimplePie.
			$feed->init();
			if($feed->error() ) {
				$this->flash($feed->error() );				
			}
			$debug->title = $feed->get_title();
			$debug->description = $feed->get_description();
			
			$record->feed_url = $feed_url;
			$record->feed_title = $feed->get_title();
			$record->feed_description = $feed->get_description();
			
        	$this->view->assign(array($varName=>$record));
 		}
 		 		
 		if($_POST['new_collection']) {
 			//$collectionID = $this->_createCollectionFromFeed($feed);
 		}
        try {
            if ($record->saveForm($_POST)) {
                $this->redirect->goto('browse');
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } catch (Exception $e) {
            $this->flash($e->getMessage());
        } 				
 	}
 	
 	public function editAction()
 	{
 		parent::editAction();
 	}
 	
 	public function historyAction() 
 	{
 		$fi_feed = $this->findById();
 		$imports = get_db()->getTable('FeedImporter_Import')->findByFeedId($fi_feed->id);
 		$this->view->assign(array('feed'=>$fi_feed , 'imports'=>$imports ) );
 	}

 	
	public function importAction() 
	{
		
		$feed = $this->findById();
		$feed_id = $feed->id;
		//make a new FI_Import
		$newImport = new FeedImporter_Import();
		$newImport->feed_id = $feed_id;
		$newImport->collection_id = $feed->collection_id;
		$newImport->status = STATUS_IN_PROGRESS_IMPORT;			
		$newImport->created = date('Y-m-d G:i:s');
        $newImport->save();
                    
        // dispatch the background process to import the items
        $user = current_user();
        $args = array();
        $args['import_id'] = $newImport->id;
        
        ProcessDispatcher::startProcess('FeedImporter_ImportProcess', $user, $args);            			
		$this->view->assign(array('import'=>$newImport, 'feed'=>$feed));
		

	}
 	
 	public function _createCollectionFromFeed($feed) 
 	{
 		$metadata = array();
 		$metadata['name'] = $feed->get_title();
 		$metadata['description'] = $feed->get_description();
 		$newCollection = insert_collection($metadata);
 		$feedimporterfeed->collection_id = $newCollection->id;
 		return $newCollection->id;
 	}
 }
?>
