<?php
class LoginController extends BaseController
{

	public $settings;
	
	public function initialize()
	{
		
	}
	
	public function onConstruct()
	{
		
	}
	

	public function notFoundAction()
	{
		// Send a HTTP 404 response header
		$this->response->setStatusCode(404, "Not Found");
	}
	
	public function indexAction()
	{
		
		if($this->session->has("userInfo")){
			return $this->response->redirect('account');
			
		}
		
		if ($this->cookies->has('username')) {

            //Get the cookie
            $rememberMe = $this->cookies->get('username');

            //Get the cookie's value
            $username = $rememberMe->getValue();
            $this->view->username = $username;

        }	
        else{
        	$this->view->flipped = 'flipped';
        	$this->view->username = '';
        }       
        
	}
	
	public function validateAction()
	{
		
		if ($this->request->isPost()) {
			
			//Receiving the variables sent by POST
			$account = $this->request->getPost('username');
			$pw = $this->request->getPost('inputPassword');
			
			$pw = md5($pw);
		
			$Login = AdminUseraccount::findFirst(array(
					"account = :id: AND pw = :pw: AND onshelf = '1'",
					"bind" => array('id' => $account,'pw' => $pw)
			));
			
			
			
			if ($Login != false) {
				$this->cookies->set('username',$account);
				$this->session->set('userInfo', $Login->toArray());
				
				return $this->response->redirect('account/list');
			}
		}	
		
		return $this->response->redirect('login');
	}	
	
	
	public function outAction()
	{
		$this->session->remove('userInfo');
		return $this->response->redirect('login');
	}
}
?>