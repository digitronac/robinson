<?php
namespace Robinson\Tests\Models;
// @codingStandardsIgnoreStart
class PackagesTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('Packages');
    }
}