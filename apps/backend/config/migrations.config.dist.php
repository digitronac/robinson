<?php 
/**
 * this configuration file is supposed to be used with phalcon migrations
 */
return new \Phalcon\Config
(
    array
    (
        'database' => array
        (
            'adapter' => 'Mysql',
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'username',
            'dbname' => 'robinson_development',
        ),
    )
);