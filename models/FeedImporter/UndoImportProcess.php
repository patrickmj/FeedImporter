<?php
/*
 * Created on Mar 25, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class FeedImporter_UndoImportProcess extends ProcessAbstract
{
    public function run($args)
    {
        $db = get_db();        
        
        // Set the memory limit.
        //$memoryLimit = get_option('feed_importer_memory_limit');
        //ini_set('memory_limit', $memoryLimit);
        
        // get the import object
        $importId = (int) $args['import_id'];
        $import = $db->getTable('FeedImporter_Import')->find($importId);
		
        // do the import
        if ($import) {
            $import->undoImport();
        }
    }
}
?>
