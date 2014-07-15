<?php

namespace Robinson\Frontend\Controllers;

class IndexController extends ControllerBase
{

    /**
     * Index page
     *
     * @return void
     */
    public function indexAction()
    {
        /** @var $package \Robinson\Frontend\Model\Package */
        $package = $this->getDI()->get('Robinson\Frontend\Model\Package');
        $this->view->lastMinutePackages = $package->findLastMinute();

        /** @var $package \Robinson\Frontend\Model\Package */
        $package = $this->getDI()->get('Robinson\Frontend\Model\Package');
        $this->view->hotPackages = $package->findHot(8);

        $this->view->popularPackages = $package->findPopular(8);

        /* leaving under comment as per request
        $this->view->topTabs = $this->makeTopTabs();
        */
        $this->view->bottomTabs = $this->makeBottomTabs(8);
        $this->view->metaDescription = \Phalcon\Tag::tagHtml('meta', array(
            'name' => 'description',
            'content' => 'Letovanje 2014 i Leto 2014 - aktuelne ponude. Vas Robinson Travel Agency.',
        ));
        $this->tag->prependTitle('Letovanje 2014');
    }

    /**
     * Info contact form.
     *
     * @throws \Exception if email is not valid
     */
    public function contactAction()
    {
        if ($this->request->getPost('email')) {
            $validator = new \Phalcon\Validation();
            $validator->add('email', new \Phalcon\Validation\Validator\Email());
            $message = $validator->validate($this->request->getPost());
            if ($message->count()) {
                throw new \Exception('Invalid email.');
            }

            $mail = new \Zend\Mail\Message();
            $mail->addTo($this->config->application->smtp->info->address);
            $mail->setSubject('Info sa kontakt forme');
            $mail->setFrom($this->request->getPost('email'));
            $mail->addReplyTo($this->request->getPost('email'));
            $body = 'Ime: ' . $this->request->getPost('name') . '<br />' . PHP_EOL;
            $body .= 'Email: ' . $this->request->getPost('email') . '<br />' . PHP_EOL;
            $body .= 'Telefon: ' . $this->request->getPost('phone') . '<br />' . PHP_EOL;
            $body .= 'Poruka: ' . $this->request->getPost('body');
            $mail->setBody($this->request->getPost('body'));

            $options = new \Zend\Mail\Transport\SmtpOptions(array(
                //'name' => 'smtp.mandrillapp.com',
                'name' => $this->config->application->smtp->name,
                //'host' => 'smtp.mandrillapp.com',
                'host' => $this->config->application->smtp->host,
                //'port' => 587,
                'port' => $this->config->application->smtp->port,
                'connection_class' => 'login',
                'connection_config' => array(
                    //'username' => $this->config->application->mail->mandrill->username,
                    'username' => $this->config->application->smtp->username,
                    //'password' => $this->config->application->mail->mandrill->password,
                    'password' => $this->config->application->smtp->password,
                    'ssl' => 'tls',
                )
            ));

            /* @var $transport \Zend\Mail\Transport\Smtp */
            $transport = $this->getDI()->get('Zend\Mail\Transport\Smtp', array($options));
            $transport->send($mail);

            $this->flashSession->message(
                'success',
                'Vaša poruka je poslata! Odgovorićemo u najkraćem mogućem roku! HVALA!!! :)'
            );
        }

        $this->tag->appendTitle('Kontakt');
    }

    /**
     * Static page.
     */
    public function usloviAction()
    {
        $this->tag->appendTitle('Opsti uslovi putovanja');
    }

    /**
     * Static page.
     */
    public function informacijeAction()
    {
        $this->tag->appendTitle('Korisne informacije');
    }

    /**
     * Static page.
     */
    public function placanjeAction()
    {
        $this->tag->appendTitle('Nacin placanja');
    }

    /**
     * Static page.
     */
    public function osiguranjeAction()
    {
        $this->tag->appendTitle('Putno osiguranje');
    }

    /**
     * Static page.
     */
    public function prtljagAction()
    {
        $this->tag->appendTitle('Dozvoljeni prtljag');
    }

    public function dokumentaAction()
    {
        $this->tag->appendTitle('Putna dokumenta');
    }

    /**
     * Page not found action.
     *
     * @return void
     */
    public function notFoundAction()
    {

    }

    /**
     * Creates array of objects that contain data for building landing page bottom tabs.
     *
     * @param int $limit limit
     *
     * @return array tabs data
     */
    protected function makeBottomTabs($limit)
    {
        $category = $this->getDI()->get('Robinson\Frontend\Model\Category');
        $tabs = array();
        foreach ($this->config->application->tabs->landing->bottom->toArray() as $key => $tab) {
            $stdClass = new \stdClass();
            $stdClass->name = $tab;
            $category = $category->findFirst(array(
                    'conditions' => "categoryId = $key AND status = 1",
                    'limit' => $limit,
                ));

            if (!$category) {
                continue;
            }
            $packages = $category->getPackagesDirectly($limit);
            $stdClass->packages = $packages;
            $tabs[] = $stdClass;
        }

        return $tabs;
    }

    /**
     * Creates array of objects that contain data for building landing page top tabs.
     *
     * @return array tabs data
     */
    protected function makeTopTabs()
    {
        $category = $this->getDI()->get('Robinson\Frontend\Model\Category');
        $tabs = array();

        foreach ($this->config->application->tabs->landing->top->toArray() as $key => $tab) {
            $stdClass = new \stdClass();
            $stdClass->name = $tab;
            $stdClass->category = $category->findFirst(array(
                    'conditions' => "categoryId = $key AND status = 1",
            ));
            $tabs[] = $stdClass;
        }

        return $tabs;
    }
}
