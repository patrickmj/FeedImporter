<?php



class FeedImporter_TagConfigTable extends Omeka_Db_Table
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
	//TODO: do I still need this?
	public function findByFeedId($feed_id, $asHash = false)
	{
		if(! $feed_id) {
			return array();
		}
		$select = $this->getSelect()->where("feed_id = ?", $feed_id);
		if($asHash) {
			$tcConfigs = $this->fetchObjects($select);
			$retArray = array();
			foreach($tcConfigs as $tcConfig) {
				$retArray[$tcConfig->original_name] = $tcConfig;
			}
			return $retArray;
		}
		return $this->fetchObjects($select);
		
	}
}

?>
