<?php
namespace Robinson\Frontend\Controllers;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        $this->view->destinations = \Robinson\Frontend\Model\Destination::find(
            array(
                'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE,
                'order' => 'destination ASC',
                'cache' => array('key' => 'find-destinations'),
            )
        );
        /*
         * removed per request
         * $this->view->lowPricePackage = \Robinson\Frontend\Model\Package::findFirst(
            array(
                'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE . ' AND price != 0',
                'order' => 'price ASC',
            )
        );*/

        $this->view->categories = $this->getCategories();

        $this->view->randomPackages = \Robinson\Frontend\Model\Package::find(
            array(
                'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE,
                'order' => 'RAND()',
                'limit' => 10,
            )
        );
        $this->tag->setTitleSeparator(' - ');
        $this->tag->setTitle('Robinson turisticka agencija');
    }

    protected function getCategories()
    {
        return array(
            array(
                'title' => 'Grčka leto 2014',
                'categoryId' => 1,
                'uri' => '/grcka-leto-2014/1'
            ),
            array(
                'title' => 'Španija leto 2014',
                'categoryId' => 2,
                'uri' => '/spanija-leto-2014/2',
            ),
            array(
                'title' => 'Italija leto 2014',
                'categoryId' => 3,
                'uri' => '/italija-leto-2014/3',
            ),
            array(
                'title' => 'Family club',
                'categoryId' => 8,
                'uri' => '/family-club/8',
            ),
            array(
                'title' => 'Leto plus',
                'categoryId' => 10,
                'uri' => '/leto-plus/10',
            ),
            array(
                'title' => 'City break',
                'categoryId' => 4,
                'uri' => '/city-break/4',
            ),
            array(
                'title' => 'Formula 1',
                'categoryId' => 7,
                'uri' => '/formula-1/7',
            ),

        );
    }
}
