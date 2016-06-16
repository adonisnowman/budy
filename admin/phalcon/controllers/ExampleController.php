<?php
class ExampleController extends BaseController
{

	public $settings;	
	public $userInfo;
	public $requestAngular;
	
	public function initialize()
	{
	
		if ($this->session->has("userInfo")) {		
			//Retrieve its value
			$this->userInfo['auto_index'] = $this->session->get("userInfo")->auto_index;
			$this->userInfo['id'] = $this->session->get("userInfo")->id;
		}
		
		$postdata = file_get_contents("php://input");		
		$this->requestAngular = json_decode($postdata,true);
		
	}
	
	
	public function notFoundAction()
	{
		
	}
	
	public function indexAction()
	{
		
		$this->action = 'menu';
		
		$this->colorpickerAction();
		return;
	}
	
	
	
	function listAction($viewType = false){
		
		
		
		$content = array();
		$viewContent['content'] = $content;
		
		
		$view = new contentView();
		$main_content = $view->getRender($this->controller, $this->action);
		
		$this->template = array(
				"main_content" => $main_content,
		);
	
	}
	function menuAction(){
	
		$content = array();
		$viewContent['content'] = $content;
	
	
		$view = new contentView();
		$main_content = $view->getRender($this->controller, $this->action);
	
		$this->template = array(
				"main_content" => $main_content,
		);
	
	}
	function colorpickerAction(){
		
		$content = array();
		$viewContent['content'] = $content;
		
		
		$view = new contentView();
		$main_content = $view->getRender($this->controller, $this->action);
		
		$this->template = array(
				"main_content" => $main_content,
		);
		
	}
	
	function datepickerAction(){
	
		$content = array();
		$viewContent['content'] = $content;
	
	
		$view = new contentView();
		$main_content = $view->getRender($this->controller, $this->action);
	
		$this->template = array(
				"main_content" => $main_content,
		);
	
	}
}
?>