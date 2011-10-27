<?php


class FeedImporter_FeedTable extends Omeka_Db_Table
{

	public function feedItemCount($feedId)
	{
		    $db = get_db();
            $select = new Omeka_Db_Select();
            $select->from(array('fiii' => $db->FeedImporterImportedItem),
                          "COUNT(DISTINCT(fiii.id))");
            $select->join(array('fii' => $db->FeedImporterImport), 'fii.id = fiii.import_id', array());
            $select->join(array('fif' => $db->FeedImporterFeed), 'fif.id = fii.feed_id', array());
            
            $select->where('fif.id = ?');
            //echo $select;
            $feedItemCount = $db->fetchOne($select, array($feedId));
            return $feedItemCount;
	}
}
?>
