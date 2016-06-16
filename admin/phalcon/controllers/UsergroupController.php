<?php
class UsergroupController extends BaseController
{

	public $settings;
	
	public function initialize()
	{
	
		if ($this->request->isPost()) {		
			$this->order_by = $this->request->getPost('order_by');
			
		}
	
	}
	
	
	public function notFoundAction()
	{
		
	}
	
	public function indexAction()
	{
		$this->action = 'list';
		$this->listAction();
		return;
	}
	
	function listAction($viewType = false){
		$return = false;
		
		$purviewAction = $this->purviewlist(1,1);
		
		$result = AdminUsergroup::find();
		$result->rewind();
		while ($result->valid()) {
			
			$Item = $result->current()->toArray();
			$Item['onshelf_css'] = ($Item['onshelf'])?'onshelf':'offshelf';
			$Item['purviewAction'] = $purviewAction;
			$Usergroup[] = $Item;
			$result->next();
		}
		
		
		$viewContent['Usergroup'] = $Usergroup;
	
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
	
	public function onshelfAction()
	{
		if ($this->request->isPost()) {
			$id = $this->request->getPost('id');

			
			$content = AdminUsergroup::findFirst(" id = $id ");			
			$add_input = array ('onshelf' =>1);
			
			if ($content->update($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
			
			echo ($content->onshelf)?'offshelf':'onshelf';
		}
		
	}
	
	public function offshelfAction()
	{
		if ($this->request->isPost()) {
			$id = $this->request->getPost('id');
		
				
			$content = AdminUsergroup::findFirst(" id = $id ");
			$add_input = array ('onshelf' =>0);
				
			if ($content->update($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
			
			echo ($content->onshelf)?'offshelf':'onshelf';
		}
	}

	public function updateAction()
	{
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
	
			
			
			$id = $info['id'];
			$purview = $info['purview'];
			$content = $info['data']::findFirst(" id = $id ");
								
			$add_input = array ('purviewlist' =>$purview);
	
			if ($content->update($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
	
		}
	
	}

}
?>