<?php
namespace Robinson\Backend\Controllers;

class IndexController extends ControllerBase
{
    /**
     * Admin login page.
     * 
     * @return mixed
     */
    public function indexAction()
    {
        /* @var $acl \Phalcon\Acl\Adapter\Memory */
        $acl = $this->di->getService('acl')->resolve();
        if ($acl->getActiveRole() !== 'Guest') {
            $this->response->redirect(
                array(
                    'for' => 'admin',
                    'controller' => 'index',
                    'action' => 'dashboard',
                )
            )->send();
        }

        if ($this->request->isPost()) {
            /* @var $loginValidator \Robinson\Backend\Validator\Login */
            $loginValidator = $this->getDI()->get(
                'Robinson\Backend\Validator\Login',
                array(
                    require MODULE_PATH . '/config/credentials.php'
                )
            );
            $isValid = $loginValidator->validate(
                array(
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                )
            );

            if ($isValid) {
                $this->session->set(
                    'auth',
                    array(
                        'username' => $this->request->getPost('username'),
                    )
                );

                $this->response->redirect(
                    array(
                        'for' => 'admin',
                        'controller' => 'index',
                        'action' => 'dashboard',
                    )
                )->send();
            }
        }
    }
    
    /**
     * Dashboard, control panel of website
     * 
     * @return void
     */
    public function dashboardAction()
    {
        /* @var $categories \Phalcon\Mvc\Model\Resultset\Simple */
        $categories = \Robinson\Backend\Models\Category::find(
            array(
                'limit' => 5,
                'status' => 1,
                'order' => 'categoryId DESC',
            )
        );
        $this->view->setVar('categories', $categories);
        
        /* @var $destinations \Phalcon\Mvc\Model\Resultset\Simple */
        $destinations = \Robinson\Backend\Models\Destination::find(
            array(
                'limit' => 5,
                'status' => 1,
                'order' => 'destinationId DESC',
            )
        );
        $this->view->setVar('destinations', $destinations);
        
        $packages = \Robinson\Backend\Models\Package::find(
            array
            (
                'limit' => 15,
                'status' => 1,
                'order' => 'packageId DESC',
            )
        );
        $this->view->packages = $packages;
    }
    
    /**
     * Destroys session and redirects to index.
     * 
     * @return \Phalcon\Http\Response
     */
    public function logoutAction()
    {
        $this->session->destroy();
        return $this->response->redirect(
            array(
                'for' => 'admin',
                'controller' => 'index',
                'action' => 'index',
            )
        );
    }

    /**
     * Sorts tagged packages.
     *
     * @return void
     */
    public function sortTaggedPackagesAction()
    {
        $this->view->tags = $this->config->application->package->tags->toArray();

        $type = \Robinson\Backend\Models\Tags\Package::TYPE_HOMEPAGE;

        if ($this->request->getPost('packageTagIds')) {
            foreach ($this->request->getPost('packageTagIds') as $packageTagId => $order) {
                $packageTag = \Robinson\Backend\Models\Tags\Package::findFirst($packageTagId);
                $packageTag->setOrder($order);
                $packageTag->save();
            }
        }

        if ($this->request->getQuery('type')) {
            $type = (int) $this->request->getQuery('type');
        }

        $this->view->packageTags = \Robinson\Backend\Models\Tags\Package::find(
            array(
                'type = ' . $type,
                'order' => "[order] ASC",
            )
        );

        $defaults = array();

        if ($this->view->packageTags) {
            foreach ($this->view->packageTags as $tag) {
                $defaults['packageTagIds[' . $tag->getPackageTagId() . ']'] = $tag->getOrder();
                //$this->tag->setDefault('packageTagIds[' . $tag->getPackageTagId() . ']', $tag->getOrder());
            }
        }

        $defaults['type'] = $type;
        $this->tag->setDefaults($defaults);
        //$this->tag->setDefault('type', $type);
    }

    /**
     * Action which manipulates with prices in pdf file for agents.
     *
     * @throws \Phalcon\Exception if pricelist cannot be created
     *
     * @return void
     */
    public function agentsAction()
    {
        if ($this->request->hasFiles('pricelists')) {
            $files = $this->request->getUploadedFiles('pricelists');
            foreach ($files as $file) {
                /** @var \Robinson\Backend\Models\Pricelist $pricelist */
                $pricelist = $this->getDI()->get('Robinson\Backend\Models\Pricelist');
                $pricelist->createFromUploadedFile($file);
            }
        }

        if ($this->request->getQuery('pricelistId')) {
            $pricelist = \Robinson\Backend\Models\Pricelist::findFirst((int) $this->request->getQuery('pricelistId'));
            $pricelist->delete();
        }

        $this->view->pricelists = \Robinson\Backend\Models\Pricelist::find(array('order' => 'filename ASC'));
    }

    public function coverAction()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $coverData = APPLICATION_PATH . '/../data/app/cover.json';
        //  submit?
        if ($this->request->isPost()) {
            // set values
            $data = new \stdClass();
            $data->text = $this->request->getPost('cover-text');
            $data->price = $this->request->getPost('cover-price', 'int');
            $data->image = $this->request->getPost('current-cover-image');
            $data->link = $this->request->getPost('cover-link');

            // we have uploaded image?
            if ($this->request->getUploadedFiles()) {
                /** @var \Phalcon\Http\Request\File $image */
                $image = $this->request->getUploadedFiles('cover-image')[0];
                $image->moveTo(APPLICATION_PATH . '/../public/img/assets/cover/' . $image->getName());
                $data->image = '/img/assets/cover/' . $image->getName();
            }
            file_put_contents($coverData, json_encode($data));
        }

        // cover data file actually there?
        if (!file_exists($coverData)) {
            return $this->response->send();
        }

        // read data
        $coverJson = file_get_contents($coverData);

        // bail out if empty
        if (!$coverJson) {
            return $this->response->send();
        }

        // decode it
        $coverJson = json_decode($coverJson);
        $this->view->cover = new \Robinson\Backend\Models\Cover($coverJson);
    }

    public function englishCoverAction()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $coverData = APPLICATION_PATH . '/../data/app/cover_en.json';
        //  submit?
        if ($this->request->isPost()) {
            // set values
            $data = new \stdClass();
            $data->text = $this->request->getPost('cover-text');
            $data->price = $this->request->getPost('cover-price', 'int');
            $data->image = $this->request->getPost('current-cover-image');
            $data->link = $this->request->getPost('cover-link');

            // we have uploaded image?
            if ($this->request->getUploadedFiles()) {
                /** @var \Phalcon\Http\Request\File $image */
                $image = $this->request->getUploadedFiles('cover-image')[0];
                $image->moveTo(APPLICATION_PATH . '/../public/img/assets/cover/en_' . $image->getName());
                $data->image = '/img/assets/cover/en_' . $image->getName();
            }
            file_put_contents($coverData, json_encode($data));
        }

        // cover data file actually there?
        if (!file_exists($coverData)) {
            return $this->response->send();
        }

        // read data
        $coverJson = file_get_contents($coverData);

        // bail out if empty
        if (!$coverJson) {
            return $this->response->send();
        }

        // decode it
        $coverJson = json_decode($coverJson);
        $this->view->cover = new \Robinson\Backend\Models\Cover($coverJson);
    }
}
