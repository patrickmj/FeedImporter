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
   		
 		$feed = new SimplePie();
 		
        $varName = strtolower($this->_modelClass);
        $class = $this->_modelClass;
        
        $record = new FeedImporter_Feed();
		//Need an id to work with the tags, so save it now, even though it might be sloppy/confusing
 		$record->save();
 		
 		if($_GET['feed_url']) {
 			$feed_url = $_GET['feed_url'];
 			$debug = new stdClass();	
			$feed->set_feed_url($feed_url);		 
			// Run SimplePie.
			$feed->init();
			$feed->handle_content_type();
			
			if($feed->error() ) {
				$this->flash($feed->error() );
				//return here?
			}
			$debug->title = $feed->get_title();
			$debug->description = $feed->get_description();
			
			//Set up the tag configurations for the first import
			$import = new FeedImporter_Import;
			$import->processFeedTags($feed, $record->id);
			
			$record->feed_url = $feed_url;
			$record->feed_title = $feed->get_title();
			$record->feed_description = $feed->get_description();
	
	 		

 		}
 		$record->save();
 		
 		// Create a new FakeCron_Task for the feed
 		$fc_task = new FakeCron_Task();
 		$fc_task->interval = 0;
 		$fc_task->name = "Cron for feed " . $record->feed_title;
 		$fc_task->plugin_class = "FeedImporter_FakeCronTask";
 		$fc_task->params = serialize(array($record->id));
 		$fc_task->save();
 		$record->task_id = $fc_task->id;
		$_POST['task_id'] = $fc_task->id;
		
    	$this->view->assign(array($varName=>$record)); 		

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
 		require_once(PLUGIN_DIR . "/FeedImporter/libraries/SimplePie/simplepie.inc");
		$feed = new SimplePie();
		$record = $this->getTable()->find($this->_getParam('id'));
		
		$feed->set_feed_url($record->feed_url);			
		$feed->init();
		$feed->handle_content_type(); 
		$import = new FeedImporter_Import;
		$import->processFeedTags($feed, $this->_getParam('id'));		
		
 		if($_POST['new_collection']) {			
 			$_POST['collection_id'] = $this->_createCollectionFromFeed($feed);	 		
 		} 		
 		
	 	// Edit the FakeCron_Task for the feed if needed
 		if($_POST['update_frequency']) {
			$fc_task = $this->getDB()->getTable('FakeCron_Task')->find($record->task_id);
	 		$fc_task->interval = $_POST['update_frequency'];
	 		$fc_task->save();
 		}

 		parent::editAction();
 	}
 	
 	public function deleteAction()
 	{
 		$feed_id = $this->_getParam('id');
		$record = $this->getTable()->find($feed_id);
 		//delete the task
		$task = $this->getDb()->getTable('FakeCron_Task')->find($record->task_id);
		$task->delete();				

 		//delete the tags configs
 		$tagConfigs = $this->getDb()->getTable('FeedImporter_TagConfig')->findBy(array('feed_id'=>$feed_id));
 		foreach($tagConfigs as $tagConfig) {
 			$tagConfig->delete();
 		}
 		
 		parent::deleteAction();
 	}
 	
 	public function historyAction() 
 	{
 		$fi_feed = $this->findById();
 		$imports = get_db()->getTable('FeedImporter_Import')->findByFeedId($fi_feed->id);
 		$this->view->assign(array('feed'=>$fi_feed , 'imports'=>$imports ) );
 	}

 	
	public function importAction() 
	{
        if (!$this->_hasValidPHPCliPath()) {
            $this->redirect->goto('error');    
        }
		$feed = $this->findById();	
				
		//make a new FI_Import
		$newImport = new FeedImporter_Import();
		$newImport->feed_id = $feed->id;
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
		$this->redirect->gotoUrl('feeds/imports/show/' . $newImport->id);

	}
 	
 	public function _createCollectionFromFeed($feed) 
 	{
 		$metadata = array();
 		$metadata['name'] = $feed->get_title();
 		$metadata['description'] = $feed->get_description();

 		
 		$newCollection = insert_collection($metadata);	
 		return $newCollection->id;
 	}
 }
?>
