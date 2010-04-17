<?php



class FeedImporter_TagConfigTable extends Omeka_Db_Table
{	
	
	public function findByOriginalName($orig_name)
	{
		
		
	}
	
	public function findByFeedId($feed_id, $asHash = false)
	{
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
	
	public function findByFeedAndOriginalName($feed_id, $orig_name)
	{
		
		
	}
	
	
	
	
	
}

?>
