<?php
namespace Robinson\Frontend\Controllers;

class PackageController extends ControllerBase
{
    protected $messages;

    public function indexAction()
    {
        $this->view->package = \Robinson\Frontend\Model\Package::findFirst(
            'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE . ' AND packageId = ' .
            (int) $this->dispatcher->getParam('id')
        );

        if ($this->request->isPost()) {
            $messages = $this->processContactForm($this->request->getPost('email'), $this->request->getPost('body'));

            if (!$messages) {
                $mail = new \Zend\Mail\Message();
                $mail->addTo('upit@robinson.rs')
                    ->addTo('ognjanovic@gmail.com');
                $mail->setSubject('Upit za "' . $this->view->package->getPackage() . '"');
                $mail->addFrom($this->request->getPost('email'));
                $mail->addReplyTo($this->request->getPost('email'));
                $mail->setBody($this->request->getPost('body'));

                $options = new \Zend\Mail\Transport\SmtpOptions(array(
                    'name' => 'smtp.mandrillapp.com',
                    'host' => 'smtp.mandrillapp.com',
                    'port' => 587,
                    'connection_class' => 'login',
                    'connection_config' => array(
                        'username' => 'ognjanovic@gmail.com',
                        'password' => 'Khzn9u0IVA7befonKv1NDA',
                    )
                ));

                $transport = new \Zend\Mail\Transport\Smtp($options);

                $transport->send($mail);


                $this->flashSession->success('Vaša poruka je poslata! Odgovorićemo u najkraćem mogućem roku! HVALA!!! :)');

                return $this->response->redirect(ltrim($this->request->getServer('REQUEST_URI') . '#contact-form', '/'));
            } else {
                foreach ($messages as $type => $message) {
                    $this->flashSession->message($type, $message);
                }

                $this->flashSession->message('email', $this->request->getPost('email'));
                $this->flashSession->message('body', $this->request->getPost('body'));

                return $this->response->redirect(ltrim($this->request->getServer('REQUEST_URI') . '#contact-form', '/'));
            }
        }


        $this->view->pdf = new \Robinson\Frontend\Model\Pdf(
            $this->fs,
            $this->view->package,
            $this->config->application->packagePdfPath
        );

        $this->view->categoryId = $this->view->package->destination->category->getCategoryId();
        $this->tag->prependTitle($this->view->package->getPackage() . ' - ');
    }

    public function pdfAction()
    {
        $this->view->package = \Robinson\Frontend\Model\Package::findFirst(
            'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE . ' AND packageId = ' .
            (int) $this->dispatcher->getParam('id')
        );

        /* @var $pdf \Robinson\Frontend\Model\Pdf */
        $pdf = $this->getDI()->get('Robinson\Frontend\Model\Pdf', array(
            $this->fs,
            $this->view->package,
            $this->config->application->packagePdfPath
        ));

        return $this->response->setContent(
            $pdf->getHtmlDocument($this->config->application->packagePdfWebPath)
                ->saveHTML()
        );
    }

    protected function processContactForm($email, $body)
    {
        $messages = array();

        $emailValidator = new \Zend\Validator\EmailAddress();
        if (!$emailValidator->isValid($email)) {
            $messages['email-error'] = $emailValidator->getMessages();
        }

        $bodyValidator = new \Zend\Validator\StringLength(array(
           'min' => 5,
        ));

        if (!$bodyValidator->isValid($body)) {
            $messages['body-error'] = $bodyValidator->getMessages();
        }

        return $messages;
    }
} 