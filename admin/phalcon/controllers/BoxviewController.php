<?php
class BoxviewController extends BaseController
{

	public $settings;
	public $facebook;
	
	public function initialize()
	{
			
	}
	
	
	public function notFoundAction()
	{
		
		
	}
	public function indexAction($viewType='')
	{
	
		
	}
	
	public function purviewlistAction($viewType='viewContent'){
		
		$content = false;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');	
			
			$result = $info['data']::findFirst("id = '".$info['id']."'");
			if($result) {
				$content = $result->toArray();
				$content['fromTable'] = $info['data'];	
				$content['controller'] = $info['controller'];
				
			}
			
		}
		
		$return = false;
		
		$Purview = AdminPurview::find()->toArray();
		$Controller = AdminController::find(' private_state = 1 ')->toArray();
		
		
		$result = AdminPurviewlist::find( array(
				"columns" => "controller_id",
				"conditions" => ' onshelf = 1 ',
				"order" => "controller_id ASC",
				"group" => "controller_id"
		));
		$result->rewind();
		while ($result->valid()) {
		
			$Item = $result->current()->toArray();
				
				
			$items = AdminPurviewlist::find( array(
					"conditions" => " onshelf = 1 AND controller_id = ".$Item['controller_id'],
					"columns" => "id,controller_id,purview_id",
					"order" => "purview_id ASC",
			));
				
			$items->rewind();
			while ($items->valid()) {
				$item = $items->current()->toArray();
				
				$item['checked'] = (in_array($item['id'], $content['purviewlist']))?'checked':'';
				$AdminPurviewlist[$item['purview_id']] = $item;
				$items->next();
			}
			$Purviewlist[$Item['controller_id']] = $AdminPurviewlist;
			unset($AdminPurviewlist);
			$result->next();
		}
		$viewContent['content'] = $content;
		$viewContent['Controller'] = $Controller;
		$viewContent['Purview'] = $Purview;
		$viewContent['Purviewlist'] = $Purviewlist;
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
	
	public function accountAction($viewType='viewContent'){
	
		$return = false;
		
		
		
		$Account = AdminAccount::find()->toArray();
		
		
		$viewContent['Account'] = $Account;
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
}
?>