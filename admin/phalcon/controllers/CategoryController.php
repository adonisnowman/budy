<?php
class CategoryController extends BaseController
{

	public $settings;
	public $facebook;
	public $purviewAction;
	
	public function initialize()
	{
	
			
	}
	
	
	public function notFoundAction()
	{
		
	}
	
	public function indexAction()
	{
				
		var_dump( UserAccount::find()->toArray());
		
		exit;
	}
	
	public function listAction($viewType = false){	
		
		$this->action = 'views';
		$this->viewsAction();
		return;
	
	}

	public function viewsAction($viewType='')
	{
	
		$return = false;
	
		$purviewAction = $this->purviewlist(1,1);
		
		$parent_category = 0;
		$ProductCategory = array();
		$result = ProductCategory::find(array(    
			"parent_category = :id: ",
			"bind" => array('id' => $parent_category),
        	"order" => "sort",        
    		));
		$result->rewind();
	
		while ($result->valid()) {
				
			$row = $result->current();
			$Item = $row->toArray();
			
			$ChlidCategory = ProductCategory::find(array(  
					"parent_category = :id: ",
					"bind" => array('id' => $Item['id']),
        			"order" => "sort",        
    		));
			
			$Item['ChlidCategory'] = ($ChlidCategory)?$ChlidCategory->toArray():array();
			 
			$Item['onshelf_css'] = ($Item['onshelf'])?'onshelf':'offshelf';
			$Item['purviewAction'] = $purviewAction;
			
			
			$ProductCategory[] = $Item;
			$result->next();
		}
	
		
		$viewContent['ProductCategory'] = $ProductCategory;
	
	
		$view = new contentView();
		$viewContent = $view->getRender($this->controller, $this->controller.$this->action,$viewContent);
		if($viewType == 'json') $return =  json_encode($content,true);
		if($viewType == 'viewContent') $return =  $viewContent;
	
		
	
		$main_content['viewContent'] = $viewContent;
	
		if($return) { echo $return; exit; }
		else{
			$main_content = $view->getRender($this->controller, $this->action,$main_content);
				
			$this->template = array(
					"main_content" => $main_content,
			);
		}
	
	}
	
	
	public function sortAction($viewType='')
	{
		if ($this->request->isPost()) {
	
			$info = $this->request->getPost('info');
	
			
			$sort = 1;
			foreach (explode(",",$info['after']) AS $id){
				$content = ProductCategory::findFirst(" id = $id ");
				$add_input = array ('sort' =>$sort);
				
				if ($content->update($add_input) == false) {
					foreach ($content->getMessages() as $message) {
						echo $message, "\n";
					}
				}
				
				$sort++;
			}
			
			
		}
	
	
	}
	
	public function insertAction($viewType='')
	{
		if ($this->request->isPost()) {
	
			$info = $this->request->getPost('info');
				
			
			if( ProductCategory::count(" name = '{$info['name']}' ") > 0){
				exit;
			}
	
			$add_input = $info;
			$add_input['sort'] = (int)(ProductCategory::count() +1 );
			var_dump($add_input);
			$content  = new ProductCategory();
	
			if ($content->create($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
	
			echo 'refresh';
		}
	
	
	}
	
	public function onshelfAction()
	{
		if ($this->request->isPost()) {
			$id = $this->request->getPost('id');
	
	
			$content = ProductCategory::findFirst(" id = $id ");
			$add_input = array ('onshelf' =>1);
	
			if ($content->update($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
	
			echo ($content->onshelf)?'onshelf':'offshelf';
		}
	
	}
	
	public function offshelfAction()
	{
		if ($this->request->isPost()) {
			$id = $this->request->getPost('id');
	
	
			$content = ProductCategory::findFirst(" id = $id ");
			$add_input = array ('onshelf' =>0);
	
			if ($content->update($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
	
			echo ($content->onshelf)?'onshelf':'offshelf';
		}
	}
}
?>