<?php
/*
 * Created on Mar 25, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class FeedImporter_ImportProcess extends ProcessAbstract
{
    public function run($args)
    {
        $db = get_db();        

        // get the import object
        $importId = (int) $args['import_id'];
        $import = $db->getTable('FeedImporter_Import')->find($importId);
		
        // do the import
        if ($import) {
            $import->doImport();
        }
    }
}
?>
