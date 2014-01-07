<?php

namespace Robinson\Backend\Controllers;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        /* @var $acl \Phalcon\Acl\Adapter\Memory */
        $acl = $this->di->getService('acl')->resolve();
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
            /* @var $loginValidator \Robinson\Backend\Validator\Login */ 
            $loginValidator = $this->getDI()->get('Robinson\Backend\Validator\Login', array(require MODULE_PATH . '/config/credentials.php'));
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
        $categories = \Robinson\Backend\Models\Category::find(array('limit' => 5, 'status' => 1, 'order' => 'categoryId'));
        $this->view->setVar('categories', $categories);
    }
}

