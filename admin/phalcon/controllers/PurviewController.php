<?php
class PurviewController extends BaseController
{

	public $settings;
	public $facebook;
	public $purviewAction;
	
	public function initialize()
	{
		
		
		
	}
	
	
	
	public function indexAction($viewType='')
	{
		
		
	}
	
	public function editAction($viewType='')
	{
	
		$return = false;
		$main_content = array();
		
		$view = new contentView();
		if($return) { echo $return; exit; }
		else{
			$main_content = $view->getRender($this->controller, $this->action,$main_content);
				
			$this->template = array(
					"main_content" => $main_content,
			);
		}
	
	}
	
	public function listAction()
	{
	
		$this->action = 'views';
		$this->viewsAction();
		return;
	}
	
	public function deleteAction($viewType='')
	{
		if ($this->request->isPost()) {
				
			$info = $this->request->getPost('info');
				
			
			$content = AdminPurviewlist::find("id = ".$info['id'])->delete();
			if($content == false) echo '無此筆資料';		
			
				
				
			echo 'refresh';
		}
	
	
	}
	
	public function insertAction($viewType='')
	{	
		if ($this->request->isPost()) {
			
			$info = $this->request->getPost('info');
			
			$Login = $this->session->get('userInfo');		
			$account_id = $Login['id'];
			
			
			if( AdminPurviewlist::count("controller_id = {$info['controller_id']} and purview_id = {$info['purview_id']}") > 0){				
				exit;
			}
			
			$add_input = $info;
			
			$content  = new AdminPurviewlist();
			$content->setAccountIndex($account_id);
			
			if ($content->create($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
			
			echo 'refresh';
		}
	
		
	}
	
	public function viewsAction($viewType='')
	{
		
		
		
		$return = false;
		
		$purviewAction = $this->purviewlist(1,1);
		
		$result = AdminPurviewlist::find(array('order'=>array('controller_id','action_type')));
		$result->rewind();
		
		while ($result->valid()) {	
			
			$row = $result->current();
			$Item = $row->toArray();
			$Item['controllerName'] = $row->AdminController->name;
			$Item['purviewName'] = $row->get_AdminPurview_field('name');
			$Item['onshelf_css'] = ($Item['onshelf'])?'onshelf':'offshelf';
			
			$Item['purviewAction'] = $purviewAction;
			$AdminPurviewlist[] = $Item;
			$result->next();
		}
		
		
		$viewContent['AdminPurviewlist'] = $AdminPurviewlist;
		$viewContent['AdminController'] = AdminController::find("private_state = 1")->toArray();
		$viewContent['AdminPurview'] = AdminPurview::find()->toArray();
		
		$view = new contentView();
		$viewContent = $view->getRender($this->controller, $this->controller.$this->action,$viewContent);
		if($viewType == 'json') $return =  json_encode($content,true);
		if($viewType == 'viewContent') $return =  $viewContent;
		
		
		
		
		$result = AdminPurview::find();
		$result->rewind();
		while ($result->valid()) {
		
			$Item = $result->current()->toArray();
			// 			$Item['onshelf_css'] = ($Item['onshelf'])?'onshelf':'offshelf';
		
			$Purview[] = $Item;
			$result->next();
		}
		
		$main_content['viewContent'] = $viewContent;
		$main_content['Purview'] = $Purview;
		
		if($return) { echo $return; exit; }
		else{
			$main_content = $view->getRender($this->controller, $this->action,$main_content);
			
			$this->template = array(
					"main_content" => $main_content,
			);
		}
		
	}
	
	
	public function updateAction()
	{
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			
			$id = $info['id'];
			$field = $info['field'];
			$value = $info['value'];
			
			
			$content = AdminPurviewlist::findFirst(" id = $id ");
			$add_input = array ($field =>$value);
				
			if ($content->update($add_input) == false) {
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
	
				
			$content = AdminPurviewlist::findFirst(" id = $id ");
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
	
	
			$content = AdminPurviewlist::findFirst(" id = $id ");
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