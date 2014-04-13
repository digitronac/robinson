<?php
namespace Robinson\Backend\Validator;

class Login extends \Phalcon\Validation
{
    private static $credentials;
    
    /**
     * Constructor
     * 
     * @param array $credentials login credentials
     */
    public function __construct($credentials)
    {
        self::$credentials = $credentials;
    }
    
    /**
     * Performs validation
     * 
     * @param array $data   array with username and password
     * @param mixed $entity entity
     * 
     * @return bool
     */
    public function validate($data = null, $entity = null)
    {
        if (!isset(self::$credentials[$data['username']])) {
            return false;
        }
        
        if (self::$credentials[$data['username']]['password'] !== $data['password']) {
            return false;
        }
        
        return true;
    }
}
