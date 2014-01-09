<?php
namespace Robinson\Backend\Controllers;
class DestinationController extends \Phalcon\Mvc\Controller
{
    /**
     * Page where detailed list of destinations is displayed.
     * 
     * @return void
     */
    public function indexAction()
    {
        $categories = \Robinson\Backend\Models\Category::find(array
        (
            'order' => 'categoryId DESC',
        ));
        
        $this->view->setVar('categories', $categories);
    }
}