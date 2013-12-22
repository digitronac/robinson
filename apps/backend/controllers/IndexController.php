<?php

namespace Robinson\Backend\Controllers;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        /* @var $acl \Phalcon\Acl\Adapter\Memory */
        $acl = $this->di->getShared('acl');
        if($acl->getActiveRole() !== 'Guest')
        {
            return $this->response->redirect(array
            (
                'for' => 'admin',
                'controller' => 'index',
                'action' => 'dashboard',
            ));
        }
        
        if($this->request->isPost())
        {
            $loginValidator = new \Robinson\Backend\Validator\Login();
            $isValid = $loginValidator->validate(array
            (
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
            ));

            if($isValid)
            {
                $this->session->set('auth', array
                (
                    'username' => $this->request->getPost('username'),
                ));
                
                return $this->response->redirect(array
                (
                    'for' => 'admin',
                    'controller' => 'index',
                    'action' => 'dashboard',
                ));
            }
        }
    }
    
    public function dashboardAction()
    {
        /* @var $categories \Phalcon\Mvc\Model\Resultset\Simple */
        $categories = \Robinson\Backend\Models\Category::find();
        $this->view->setVar('categories', $categories);
    }
    
    public function testAction()
    {
        var_dump($this->dispatcher->getParams());
    }

}

