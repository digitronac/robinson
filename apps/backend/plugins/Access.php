<?php
namespace Robinson\Backend\Plugin;
class Access extends \Phalcon\Mvc\User\Plugin
{
    /**
     * Checks if visitor is allowed to access pages.
     * 
     * @param \Phalcon\Events\Event   $event      event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher dispatcher
     * 
     * @return mixed
     */
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $auth = $this->getDI()->getShared('session')->get('auth');
        $role = (!$auth) ? 'Guest' : 'User';

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        
        /* @var $acl \Phalcon\Acl\Adapter\Memory */
        $acl = $this->getDI()->getShared('acl');
        
        /* @var $request \Phalcon\Http\Request */
        $request = $this->getDI()->getShared('request');

        if (!$acl->isAllowed($role, $controller, $action))
        {
            return $this->dispatcher->forward(array
            (
                'controller' => 'index',
                'action' => 'index',
            ));
        }
    }
}