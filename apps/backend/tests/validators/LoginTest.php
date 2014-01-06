<?php
namespace Robinson\Backend\Tests\Validator;
class LoginTest extends \Phalcon\Test\UnitTestCase
{
    public function testCanCreateLoginValidator()
    {
        $validator = new \Robinson\Backend\Validator\Login(include __DIR__ . '/../fixtures/credentials.php');
        $this->assertInstanceOf('Robinson\Backend\Validator\Login', $validator);
    }
    
    public function testValidLoginShouldReturnTrue()
    {
        $validator = new \Robinson\Backend\Validator\Login(include __DIR__ . '/../fixtures/credentials.php');
        $result = $validator->validate(array
        (
           'username' => 'test',
           'password' => 'testpassword',
        ));
        $this->assertTrue($result);
    }
    
    public function testInvalidUsernameLoginShouldReturnFalse()
    {
        $validator = new \Robinson\Backend\Validator\Login(include __DIR__ . '/../fixtures/credentials.php');
        $result = $validator->validate(array
        (
           'username' => 'testwrongusername',
           'password' => 'testpassword',
        ));
        $this->assertFalse($result);
    }
    
    public function testInvalidPasswordLoginShouldReturnFalse()
    {
        $validator = new \Robinson\Backend\Validator\Login(include __DIR__ . '/../fixtures/credentials.php');
        $result = $validator->validate(array
        (
           'username' => 'test',
           'password' => 'testinvalidpasswordlogin',
        ));
        $this->assertFalse($result);
    }
}