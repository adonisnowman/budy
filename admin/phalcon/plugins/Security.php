<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Phalcon\Mvc\Dispatcher\Exception as DispatchException,
	Phalcon\Acl;


class Security extends Plugin
{

	public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
    	
    	
    }

    public function beforeException(Event $event, Dispatcher $dispatcher, $exception)
    {
    	
    	//Handle 404 exceptions
    	if ($exception instanceof DispatchException) {
    		$this->flash->error("Handle 404 exceptions");
    		$this->response->redirect('login');

    	}
    
    	//Handle other exceptions
    	$this->flash->error("Handle 503 exceptions");
  
    	return false;
    }    
    
	public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
    	
    	$role = 'Users';
    	$privateResources = array();
        if ($this->session->has("userInfo")) {

        	 
        	foreach (AdminController::find(" private_state = 1 ") AS $v){
        		$privateResources[$v->code] = array('*');
        	}      		
        
        	
        }
        
      
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();


        $acl = $this->getAcl($privateResources);
		
        
        $allowed = $acl->isAllowed($role, $controller, $action);
      
        if ($allowed != Acl::ALLOW) {
        	
        	
        	if($controller != 'login'){
        	
        	
        		return $this->dispatcher->forward(array(
        				'controller' => 'login',
        				'action' => 'index'
        		));
        	}
        	
            return false;
        }else{
			
        	
        }

    }
    
    public function getAcl($privateResources = array())
    {
    	$publicResources = array();
    	
    
	    
		$acl = new Phalcon\Acl\Adapter\Memory();
		
		
		$acl->setDefaultAction(Phalcon\Acl::DENY);
		
		$acl->addRole(new Phalcon\Acl\Role('Users'));
		
		
		
		
   		foreach (AdminController::find(" private_state = 0 ") AS $v){	
				$publicResources[$v->code] = array('*');
		}
		
		
		foreach ($publicResources as $resource => $actions) {
			$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
		}
		
		
		
		foreach ($publicResources as $resource => $actions) {				
			foreach ($actions as $action) {
				$acl->allow('Users', $resource, $action);
			}
		}
		
		foreach ($privateResources as $resource => $actions) {
			$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
		}
		//Grant access to private area only to role Users
		foreach ($privateResources as $resource => $actions) {
			foreach ($actions as $action) {
				$acl->allow('Users', $resource, $action);
			}
		}
		
		return $acl;
    }

}