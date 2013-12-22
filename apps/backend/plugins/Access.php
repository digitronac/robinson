<?php
namespace Robinson\Backend\Plugin;
class Access extends \Phalcon\Mvc\User\Plugin
{
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $auth = $this->session->get('auth');
        $role = (!$auth) ? 'Guest' : 'User';

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        
        /* @var $acl \Phalcon\Acl\Adapter\Memory */
        $acl = $this->getDI()->getShared('acl');
        
        /* @var $request \Phalcon\Http\Request */
        $request = $this->getDI()->getShared('request');

        if(!$acl->isAllowed($role, $controller, $action))
        {
            return $this->dispatcher->forward(array
            (
                'controller' => 'index',
                'action' => 'index',
            ));
        }
    }
}