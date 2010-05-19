<?php


class FeedImporter_AuthorConfigTable extends Omeka_Db_Table
{
	public function applySearchFilters($select, $params)
	{
		if(isset($params['feed_id'])) {
			$this->filterByFeedId($select, $params['feed_id']);
		}
		
	}

	public function filterByFeedId($select, $feed_id)
	{
		$select->where( "feed_id = $feed_id" );
		return $select;		
	}	
}
?>
