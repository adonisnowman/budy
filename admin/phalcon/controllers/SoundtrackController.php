<?php
class SoundtrackController extends BaseController
{

	public $settings;
	public $facebook;
	public $purviewAction;
	
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
		
		$Login = Soundtrack::findFirst();
		var_dump($Login->toArray());
		exit;
		echo $account = BlogTest::count();
		echo $account = Test::count();
		exit;
	}
	
	public function listAction($viewType = false){
		
		
		$this->action = 'views';
		$this->viewsAction();
		return;
	
	}

	public function insertAction($viewType='')
	{
		if ($this->request->isPost()) {
				
			$info = $this->request->getPost('info');
							
			var_dump($info);
			if( Soundtrack::count(" video_id = '{$info['video_id']}' ") > 0){
				exit;
			}
				
			$add_input = $info['post'];
			$add_input['video_id'] = $info['video_id'];
			$add_input['category_id'] = $info['category_id'];
			
			$content  = new Soundtrack();
				
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
		$Soundtrack = array();
		$purviewAction = $this->purviewlist(1,1);
		$ProductCategory = ProductCategory::find(array('parent_category = 24 AND onshelf = 1 ',"order" => "sort ASC"));
		
		$where = array( ' onshelf = 1 ', "order" => "update_time ASC");
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			$where['bind'] = $info;
			foreach($info AS $key=> $v){
				$where[0] .= " AND $key = :$key: ";
			}
				
		}
		
		
		
		
		
		
		$result = Soundtrack::find($where);
		$result->rewind();
		
		while ($result->valid()) {	
			
			$row = $result->current();
			$Item = $row->toArray();			
			$Item['ProductCategory'] = $row->ProductCategory->name;
			$Item['onshelf_css'] = ($Item['onshelf'])?'onshelf':'offshelf';
			
			$Item['purviewAction'] = $purviewAction;
			$Soundtrack[] = $Item;
			$result->next();
		}
		
		$viewContent['ProductCategory'] = (false !== $ProductCategory)?$ProductCategory->toArray():array();
		
		foreach($viewContent['ProductCategory'] AS $key => $value){	
				$viewContent['ProductCategory'][$key]['selected'] = (isset($info['category_id']) && $value['id'] == $info['category_id'])?"selected":"";				
		}
		$viewContent['Soundtrack'] = $Soundtrack;
		
		
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
	
	public function updateAction()
	{
	if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
	
			$id = $info['id'];
			$perview = $info['preview'];
			$content = $info['data']::findFirst(" id = $id ");
								
			foreach($perview AS $key => $value)
				$content->$key = $value;
			
	
			if ($content->save() == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
	
		}
	
	}
	
	function relativePurview($groupPurview,$purview){
		return $fullDiff = array_merge(array_diff($groupPurview, $purview), array_diff($purview, $groupPurview));
		
	}
	
	public function onshelfAction()
	{
		if ($this->request->isPost()) {
			$id = $this->request->getPost('id');
	
	
			$content = Soundtrack::findFirst(" id = $id ");
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
	
	
			$content = Soundtrack::findFirst(" id = $id ");
			$add_input = array ('onshelf' =>0);
	
			if ($content->update($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
	
			echo ($content->onshelf)?'onshelf':'offshelf';
		}
	}
	
	
	
	
	function groupAction($viewType = false,$index = false){
		
		if($index) $content = AdminGroup::findFirst("auto_index=".$index);
		else $content = AdminGroup::find();
		$viewContent['content'] = $content->toArray();
		
		if($viewType == 'json') return json_encode($viewContent['content'],true);
		if($viewType == 'array') return $viewContent['content'];
		$view = new contentView('./app/views');
		$main_content = $view->getRender($this->controller, $this->action,$viewContent);
		

		$this->template = array(
			"main_content" => $main_content,
		);
		
	}
	
	function actionsAction($action = false) {
		
		if ($this->request->isPost()) {
			$Login = $this->session->get('userInfo')->toArray();
				
			$date = date("Y-m-d H:i:s");
			$account_id = $Login['auto_index'];
			
			
			
			if($action == 'edit_password'){
				$index = $this->request->getPost('index');
				$content = AdminAccount::findFirst("auto_index = '".$index."'");
				
				if($content != false){
						
						
					$content =$content->toArray();
						
					$this->view->account_info = $content;
						
				}
				
				echo  $this->view->start()->render('account', 'editPw')->finish()->getContent();
				
			}

			if($action == 'add_account') {
				
				$index = $this->request->getPost('index');
				
				$AdminMenu = AdminMenu::find()->toArray();
				$competence = AdminGroup::find()->toArray();
					
				$this->view->competence = $competence;
				$this->view->admin_competence = array('A'=>'新增','D'=>'刪除','E'=>'修改','V'=>'觀看','O'=>'上下架');
				$this->view->AdminMenu = $AdminMenu;
				
				
				$content = AdminAccount::findFirst("auto_index = '".$index."'");
				
				if($content != false){
					
					
					$content =$content->toArray();
					$content['account_competence'] = json_decode($content['competence'],true);					
					
					$this->view->account_info = $content;
					
				}
				
				echo  $this->view->start()->render('account', 'edit')->finish()->getContent();
					
			}
			
			if($action == 'validate_password'){
				
				$index = $this->request->getPost('auto_index');
				$pw =  $this->request->getPost('pw');
				$confim_pw =  $this->request->getPost('confim_pw');
				$content = AdminAccount::findFirst("auto_index = '".$index."'");
				
				if($content != false && $pw == $confim_pw) {
					$add_input['pw'] = md5($pw);
					$add_input['create_time'] = $date;
					$add_input['update_time'] = $date;
					$add_input['create_account_index'] = $account_id;
					$add_input['update_account_index'] = $account_id;
					
					$content->update($add_input);
				}
			}
			
			if($action == 'validate') {
				
				$index = $this->request->getPost('auto_index');
				
				$online = '1';
				$add_input = array("id"=>'','pw'=>'','admingroup_index'=>0,'competence'=>'','online'=>$online,'memo'=>'');
					
				foreach ($add_input AS $key => $value){
					if(!$value){
						switch ($key){
							case "competence":
								$add_input[$key] = json_encode( $this->request->getPost('competence'),true);
								break;
							default:
								$add_input[$key] = $this->request->getPost($key);
								break;
						}
					}
				}
				
				$content = AdminAccount::findFirst("auto_index = '".$index."'");
			
				if($content != false){
					unset($add_input['pw']);
					
					$add_input['update_time'] = $date;
					$add_input['update_account_index'] = $account_id;
					
					$content->update($add_input);
				}else{
					$add_input['pw'] = md5($add_input['pw']);
					$add_input['create_time'] = $date;
					$add_input['update_time'] = $date;
					$add_input['create_account_index'] = $account_id;
					$add_input['update_account_index'] = $account_id;					
					
					$content = new AdminAccount();					
					$insert_check = $content->create($add_input);
					
					if(!$insert_check) echo 'Error Insert';
					
				}
					
			}
				
		}
		
		
		
		

	}
	
}
?>