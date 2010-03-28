<?php


class FeedImporter_ImportTable extends Omeka_Db_Table
{


	public function getMostRecentImportForFeedId($feed_id)
	{
		$sel = $this->getSelect()->where("feed_id = ?", $feed_id)->order('created DESC') ;
		return $this->fetchObject($sel);		
	}

	public function findByFeedId($feed_id)
	{
		$sel = $this->getSelect()->where("feed_id = ?", $feed_id)->order('created DESC') ;
		return $this->fetchObjects($sel);		
	}
}

?>
