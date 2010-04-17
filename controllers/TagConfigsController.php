<?php





class FeedImporter_TagConfigsController extends Omeka_Controller_Action
{
    public function init() 
    {
        $this->_modelClass = 'FeedImporter_TagConfig';
    } 
	
	
	public function browseAction()
	{

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
		$page = $this->_getParam('page');
		$table = $this->getTable();
		$fi_tags = $table->findBy(array(), 10, $page);
		$count = $table->fetchOne($table->getSelectForCount() ) ;
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
		
		$this->view->assign(array('tags'=>$fi_tags,  'debug'=>$paginator));
	}	
}


?>
