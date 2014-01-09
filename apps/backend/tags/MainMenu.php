<?php
namespace Robinson\Backend\Tag;
class MainMenu extends \Phalcon\Tag
{
    /**
     * Renders main menu.
     * 
     * @return string menu
     */
    public function mainMenu()
    {
        $dispatcher = $this->retrieveDispatcher();
        
        if ($dispatcher->getControllerName() === 'index' 
            && ($dispatcher->getActionName() === 'index' 
            || $dispatcher->getActionName() === 'dashboard'))
        {
            return;
        }
        
        return $this->getDI()->getShared('view')->partial('partials/nav');
    }
            
    /**
     * Retrieves dispatcher from di.
     * 
     * @return \Phalcon\Mvc\Dispatcher
     */
    protected function retrieveDispatcher()
    {
        return $this->getDI()->getShared('dispatcher');
    }
}