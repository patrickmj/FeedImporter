<?php
/*
 * Created on Mar 25, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
class FeedImporter_ImportsController extends Omeka_Controller_Action 
{
 	
    public function init() 
    {
        $this->_modelClass = 'FeedImporter_Import';
    }
    
	public function undoimportAction()
    {
		$fi_import = $this->findById();
        // change the status of the import
        $fi_import->status = FeedImporter_Import::STATUS_IN_PROGRESS_UNDO_IMPORT;
        $fi_import->save();

        // // dispatch the background process to undo the import
        $user = current_user();
        $args = array();
        $args['import_id'] = $fi_import->id;
        ProcessDispatcher::startProcess('FeedImporter_UndoImportProcess', $user, $args);
		
       // $this->flashSuccess("Successfully started to undo the import. Reload this page for status updates.");
       $this->redirect->gotoUrl('feeds/show/' . $fi_import->feed_id);
        //$this->redirect->gotoUrl('feeds/imports/show/' . $fi_import->id );
    }
    
    public function itemsAction()
    {
    	$import = $this->findById();
    	$items = $import->getItems();
    	$feed = get_Db()->getTable('FeedImporter_Feed')->find($import->feed_id);
    	$this->view->assign(array('items'=>$items, 'import'=>$import, 'feed'=>$feed));    	
    }

    
    
     	 	
}
 
 
 
 
?>
