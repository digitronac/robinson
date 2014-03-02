<?php
namespace Robinson\Frontend\Tests\Controllers;

class IndexControllerTest extends BaseTestController
{
    protected $vfsStream;
    
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->vfsStream = \org\bovigo\vfs\vfsStream::setup('root', null, array(
            'img' => array(
                'package' => array(
                    '1-testpackageimage-1.jpg' => file_get_contents(__DIR__ . '/_files/images/46-15.JPG'),
                ),
            ),
        ));
        $this->populateTable('packages');
        $this->populateTable('package_tabs');
        $this->populateTable('package_tags');
    }

    public function testIndexActionShouldWorkAsExpected()
    {
        $this->getDI()->getShared('config')->application->packageImagesPath = 'vfs://root/img/package';

        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
        echo $this->getDI()->getShared('response')->getContent();
        die();
        $this->assertResponseContentContains();
    }
} 