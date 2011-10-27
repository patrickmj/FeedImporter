<?php





class FeedImporter_TagConfigsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'FeedImporter_TagConfig';
    }
	
	
	public function browseAction()
	{
		//TODO: select by feed_id
		if($_POST) {
			foreach($_POST['tc'] as $tcId=>$data) {
				$record = $this->getTable()->find($tcId);
				foreach($data as $field=>$value) {
					switch($field) {
						case 'elements_map':
						case 'tags_map':
						$value = serialize($value);
						break;
					}
				$record->$field = $value;
				$record->save();
				}
			}
		}
		$page = $this->_getParam('page', 1);
		
		$table = $this->getTable();
		$feed_id = $this->_getParam('feed_id');
		
		$fi_tags = $table->findBy(array('feed_id'=>$feed_id), 10, $page);
		$count = $table->fetchOne($table->getSelectForCount()->where('feed_id = ?', $feed_id) ) ;
        /**
         * Now process the pagination
         *
         **/
        $paginationUrl = $this->getRequest()->getBaseUrl().'/tags/browse/';

        //Serve up the pagination
        $pagination = array('page'          => $page,
                            'per_page'      => 10,
                            'total_results' => $count,
                            'link'          => $paginationUrl);
        
        Zend_Registry::set('pagination', $pagination);
		
		$db = get_db();
		$elSets = $db->getTable('ElementSet')->findAll();
		//print_r($elSets);
		$elSetPairs = array();
		foreach($elSets as $elSet) {
			$elSetPairs[$elSet->id] = $elSet->name;
			release_object($elSet);
		}
		
		$tagTable = $db->getTable('Tag');
		$o_tags = $tagTable->findBy(array('sort' => 'alpha'));
		$tagPairs = array();
		foreach($o_tags as $o_tag) {
			$tagPairs[$o_tag->id] = $o_tag->name;
			release_object($o_tag);
		}
		
		$priorityArray = range(1, 10);
		$this->view->assign(array('tags'=>$fi_tags,
									'debug'=>$pagination,
									'elSetPairs'=>$elSetPairs,
									'tagPairs'=>$tagPairs,
									'priorityArray'=>$priorityArray,
									'feed_id'=>$feed_id));
	}
}


?>
