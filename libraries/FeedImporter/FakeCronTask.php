<?php


class FeedImporter_FakeCronTask implements FakeCron_TaskInterface
{
	public function run($params = null)
	{
		$params = unserialize($params);
		$feed_id = $params[0];
		$feed = get_db()->getTable('FeedImporter_Feed')->find($feed_id);
						
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
	}		
}
?>
