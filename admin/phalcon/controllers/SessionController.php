<?php


class SessionController extends BaseController
{

	protected function initialize()
	{
		//Prepend the application name to the title
		$this->tag->prependTitle('INVO | ');
	}
   
    private function _registerSession($Article)
    {
        $this->session->set('auth', array(
            'id' => $Article->auto_index
        ));
    }

    public function startAction()
    {
        if ($this->request->isPost()) {

            //Receiving the variables sent by POST
            $email = $this->request->getPost('email', 'email');
            $password = $this->request->getPost('password');

            $password = sha1($password);

            //Find the user in the database
            $Article = AdminAccount::findFirst(array(
                "auto_index = :auto_index: AND online = '1'",
                "bind" => array('auto_index' => $email)
            ));
            if ($Article != false) {

                $this->_registerSession($Article);

                $this->flash->success('Welcome ' . $Article->id);

                //Forward to the 'invoices' controller if the user is valid
                return $this->dispatcher->forward(array(
                    'controller' => 'session',
                    'action' => 'index'
                ));
            }

            $this->flash->error('Wrong email/password');
        }

        //Forward to the login form again
        return $this->dispatcher->forward(array(
            'controller' => 'session',
            'action' => 'index'
        ));

    }

}