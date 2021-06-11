<?php
namespace Robinson\Frontend\Controllers;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        $this->upToDateUri();
        /** @var $package \Robinson\Frontend\Model\Package */
        $package = $this->getDI()->get('Robinson\Frontend\Model\Package');
        $this->view->randomEnglishPackages = $package->findRandomEnglishPackages();
        $this->view->destinations = \Robinson\Frontend\Model\Destination::find(
            array(
                'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE,
                'order' => 'destination ASC',
                'cache' => array('key' => 'find-destinations'),
            )
        );

        $this->view->season = $this->getDI()->get('config')->application->season;

        $this->view->categories = $this->getCategories();
        if (
            strpos(
                $this->getDI()->getShared('config')->application->baseUrls->enBaseUrl,
                $this->request->getServer('HTTP_HOST')
            ) !== false
        ) {
            $this->view->categories = $this->getEnglishCategories();
        }
        $this->view->baseUrls = $this->getDI()->get('config')->application->baseUrls;

        $this->view->randomPackages = $this->findRandomPackages();
        $this->tag->setTitleSeparator(' - ');
        $this->tag->setTitle('Robinson');
        if (APPLICATION_ENV !== 'testing' && $_SERVER['HTTP_HOST'] === 'insideserbia.com') {
            $this->tag->setTitle('InSide Serbia');
        }

        $this->view->pages = \Robinson\Frontend\Model\Page::find(array('order' => 'pageId ASC'));
        $this->view->cover = new \Robinson\Frontend\Model\Cover(
            json_decode(
                file_get_contents(APPLICATION_PATH . '/../data/app/cover.json')
            )
        );
        $this->view->englishCover = new \Robinson\Frontend\Model\Cover(
            json_decode(
                file_get_contents(APPLICATION_PATH . '/../data/app/cover_en.json')
            )
        );
    }

    public function findRandomPackages()
    {
        $englishCategoryIds = $this->getDI()->get('config')->application->insideserbia->categoryIds->toArray();
        $englishCategoryIdsForQuery = implode(',', $englishCategoryIds);
        $query = $this->getDI()->get('modelsManager')->createQuery(
            'SELECT packages.* FROM Robinson\Frontend\Model\Package AS packages JOIN
            Robinson\Frontend\Model\Destination as destinations
            ON packages.destinationId = destinations.destinationId
            WHERE packages.status = 1 AND destinations.categoryId NOT IN ("' . $englishCategoryIdsForQuery . '") ORDER BY RAND() LIMIT 10'
        );
        $query->cache(array(
            'key' => 'find-random-packages',
        ));
        return $query->execute();
    }

    protected function getCategories()
    {
        $baseUrls = $this->getDI()->get('config')->application->baseUrls;
        return array(
            array(
                'title' => 'Grčka 2021',
                'categoryId' => 1,
                'uri' => $baseUrls['rsBaseUrl'] . '/grcka-leto-2021/1',
                'decorated' => false,
            ),
            array(
                'title' => 'Albanija 2021',
                'categoryId' => 20,
                'uri' => $baseUrls['rsBaseUrl'] . '/albanija-leto-2021/20',
                'decorated' => false,
            ),
            array(
                'title' => 'Turska 2021',
                'categoryId' => 18,
                'uri' => $baseUrls['rsBaseUrl'] . '/turska-leto-2021/18',
            ),
            /*array(
                'title' => 'Nova Godina 2021',
                'categoryId' => 12,
                'uri' => $baseUrls['rsBaseUrl'] . '/nova-godina-2021/12',
                'decorated' => false,
            ),
            array(
                'title' => 'Zimovanje 2021',
                'categoryId' => 13,
                'uri' => $baseUrls['rsBaseUrl'] . '/zimovanje-2021/13',
                'decorated' => false,
            ),*/
            /*array(
                'title' => 'Egipat 2021',
                'categoryId' => 17,
                'uri' => $baseUrls['rsBaseUrl'] . '/egipat/17',
                'decorated' => false,
            ),
            */
            array(
                'title' => 'Ohrid 2021',
                'categoryId' => 23,
                'uri' => $baseUrls['rsBaseUrl'] . '/ohrid/23',
                'decorated' => false,
            ),
            /*array(
                'title' => 'City break',
                'categoryId' => 4,
                'uri' => $baseUrls['rsBaseUrl'] . '/city-break/4',
                'decorated' => false,
            ),*/
            array(
                'title' => 'Formula 1',
                'categoryId' => 7,
                'uri' => $baseUrls['rsBaseUrl'] . '/formula-1/7',
                'decorated' => false,
            ),
            array(
                'title' => 'Srbija odmor',
                'categoryId' => 19,
                'uri' => $baseUrls['rsBaseUrl'] . '/srbija-odmor/19',
                'decorated' => false,
            ),
            array(
                'title' => 'Skrivena Srbija',
                'categoryId' => 11,
                'uri' => $baseUrls['rsBaseUrl'] . '/skrivena-srbija/11',
                'decorated' => false,
            ),
            /*array(
                'title' => 'Wellness I Spa',
                'categoryId' => 22,
                'uri' => $baseUrls['rsBaseUrl'] . '/wellness-i-spa/22',
                'decorated' => false,
            ),*/
            array(
                'title' => 'InSide Serbia',
                'categoryId' => null,
                'uri' => 'http://insideserbia.com/inside-serbia/14',
                'decorated' => false,
            ),
            /*array(
                'title' => 'Grčka leto 2020',
                'categoryId' => 1,
                'uri' => $baseUrls['rsBaseUrl'] . '/grcka-leto-2020/1',
                'decorated' => false,
            ),
            array(
                'title' => 'Turska leto 2020',
                'categoryId' => 18,
                'uri' => $baseUrls['rsBaseUrl'] . '/turska-leto-2020/18',
            ),
            array(
                'title' => 'Albanija leto 2020',
                'categoryId' => 20,
                'uri' => $baseUrls['rsBaseUrl'] . '/albanija-leto-2020/20',
                'decorated' => false,
            ),
            array(
                'title' => 'Crna Gora leto 2020',
                'categoryId' => 21,
                'uri' => $baseUrls['rsBaseUrl'] . '/crna-gora-2020/21',
                'decorated' => false,
            ),
            /*array(
                'title' => 'Španija 2020',
                'categoryId' => 2,
                'uri' => $baseUrls['rsBaseUrl'] . '/spanija-leto-2020/2',
                'decorated' => false,
            ),*/
            /*array(
                'title' => 'Italija 2020',
                'categoryId' => 3,
                'uri' => $baseUrls['rsBaseUrl'] . '/italija-leto-2020/3',
                'decorated' => false,
            ),*/
        );
    }

    protected function getEnglishCategories()
    {
        $baseUrls = $this->getDI()->get('config')->application->baseUrls;
        return array(
            array(
                'title' => 'InSide Serbia',
                'categoryId' => 14,
                'uri' => $baseUrls['enBaseUrl'] . '/inside-serbia/14',
                'decorated' => false,
            ),
        );
    }

    /**
     * Incredibly quick and incredibly dirty fix for change of season in links :)
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    protected function upToDateUri()
    {
        if (strpos($this->router->getRewriteUri(), '2020')) {
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
