<?php
namespace Robinson\Backend\Validator;
class Login extends \Phalcon\Validation
{
    private static $credentials;
    
    public function __construct($credentials)
    {
        self::$credentials = $credentials;
    }
    
    public function validate($data = null, $entity = null)
    {
        if(!isset(self::$credentials[$data['username']]))
        {
            return false;
        }
        
        if(self::$credentials[$data['username']]['password'] !== $data['password'])
        {
            return false;   
        }
        
        return true;
    }
}