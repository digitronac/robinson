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

        // no package? redirect to index
        if (!$this->view->package) {
            return $this->response->redirect('/', true);
        }

        if ($this->request->isPost()) {
            $messages = $this->processContactForm($this->request->getPost('email'), $this->request->getPost('body'));

            if (!$messages) {
                $mail = new \Zend\Mail\Message();
                $mail->addTo($this->config->application->smtp->package->address);
                $mail->setSubject('Upit za "' . $this->view->package->getPackage() . '"');
                $mail->addFrom($this->request->getPost('email'));
                $mail->addReplyTo($this->request->getPost('email'));
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

                return $this->response->redirect(
                    ltrim($this->request->getServer('REQUEST_URI') . '#contact-form', '/')
                )->send();
            } else {
                foreach ($messages as $type => $message) {
                    $this->flashSession->message($type, $message);
                }

                $this->flashSession->message('email', $this->request->getPost('email'));
                $this->flashSession->message('body', $this->request->getPost('body'));

                return $this->response->redirect(
                    ltrim($this->request->getServer('REQUEST_URI') . '#contact-form', '/')
                );
            }
        }


        $this->view->pdf = new \Robinson\Frontend\Model\Pdf(
            $this->fs,
            $this->view->package,
            $this->config->application->packagePdfPath
        );

        $this->view->categoryId = $this->view->package->destination->category->getCategoryId();
        $destination = $this->view->package->getRelated('destination');

        $this->tag->prependTitle($destination->getRelated('category')->getCategory());
        $this->tag->prependTitle($destination->getDestination());
        $this->tag->prependTitle($this->view->package->getPackage());
        $this->view->metaDescription = \Phalcon\Tag::tagHtml('meta', array(
            'name' => 'description',
            'content' => $this->view->package->getPackage() . ' - Aranzman - Opis - Cene - Rezervacija.',
        ));
    }

    public function pdfAction()
    {
        $this->view->package = \Robinson\Frontend\Model\Package::findFirst(
            'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE . ' AND packageId = ' .
            (int) $this->dispatcher->getParam('id')
        );

        $pdfType = ($this->request->getQuery('pdfType')) ? (int) $this->request->getQuery('pdfType')
            : \Robinson\Frontend\Model\Pdf::PDF_FIRST;

        /* @var $pdf \Robinson\Frontend\Model\Pdf */
        $pdf = $this->getDI()->get('Robinson\Frontend\Model\Pdf', array(
            $this->fs,
            $this->view->package,
            $this->config->application->packagePdfPath,
            $pdfType,
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
