<?php
namespace Robinson\Backend\Validator;
class Login extends \Phalcon\Validation
{
    private static $credentials = array
    (
        'nemanja' => array
        (
            'password' => 'robinson',
        ),
    );
    
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