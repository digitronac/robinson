<?php
namespace Robinson\Frontend\Controllers;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        $this->upToDateUri();
        $this->view->destinations = \Robinson\Frontend\Model\Destination::find(
            array(
                'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE,
                'order' => 'destination ASC',
                'cache' => array('key' => 'find-destinations'),
            )
        );

        $this->view->season = $this->getDI()->get('config')->application->season;

        $this->view->categories = $this->getCategories();

        $this->view->randomPackages = \Robinson\Frontend\Model\Package::find(
            array(
                'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE,
                'order' => 'RAND()',
                'limit' => 10,
                'cache' => array(
                    'key' => 'find-random-packages',
                ),
            )
        );
        $this->tag->setTitleSeparator(' - ');
        $this->tag->setTitle('Robinson');

        $this->view->pages = \Robinson\Frontend\Model\Page::find(array('order' => 'pageId ASC'));
        $this->view->cover = new \Robinson\Frontend\Model\Cover(
            json_decode(
                file_get_contents(APPLICATION_PATH . '/../data/app/cover.json')
            )
        );
    }

    protected function getCategories()
    {
        return array(
            /*array(
                'title' => 'Nova godina 2016',
                'categoryId' => 12,
                'uri' => '/nova-godina-2016/12',
            ),*/
            array(
                'title' => 'Grčka leto 2016',
                'categoryId' => 1,
                'uri' => '/grcka-leto-2016/1',
                'decorated' => false,
            ),
            array(
                'title' => 'Španija leto 2016',
                'categoryId' => 2,
                'uri' => '/spanija-leto-2016/2',
                'decorated' => false,
            ),
            array(
                'title' => 'Italija leto 2016',
                'categoryId' => 3,
                'uri' => '/italija-leto-2016/3',
                'decorated' => false,
            ),
            array(
                'title' => 'Family club',
                'categoryId' => 8,
                'uri' => '/family-club/8',
                'decorated' => false,
            ),
            array(
                'title' => 'Skrivena Srbija',
                'categoryId' => 11,
                'uri' => '/skrivena-srbija/11',
                'decorated' => false,
            ),
            array(
                'title' => 'City break',
                'categoryId' => 4,
                'uri' => '/city-break/4',
                'decorated' => false,
            ),
            array(
                'title' => 'Formula 1',
                'categoryId' => 7,
                'uri' => '/formula-1/7',
                'decorated' => false,
            ),
            array(
                'title' => 'Last minute',
                'categoryId' => null,
                'uri' => '/index/lastMinute',
                'decorated' => true,
            )
        );
    }

    /**
     * Incredibly quick and incredibly dirty fix for change of season in links :)
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    protected function upToDateUri()
    {
        if (strpos($this->router->getRewriteUri(), '2015') !== false) {
            return $this->response->redirect(
                str_replace(
                    ((int)$this->config->application->season->year) - 1,
                    $this->config->application->season->year,
                    $this->router->getRewriteUri()
                ),
                true,
                301
            )
            ->send();
        }
    }
}
