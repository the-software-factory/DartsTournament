<?php

define('APPLICATION_PATH', 'application');
define('DB_HOST', 'localhost');
define('DB_NAME', 'darts_tournament_test');
define('DB_USER', 'root');
define('DB_PASS', 'password');


$absApplicationRoot = realpath('application');
$loader = new Zend_Application_Module_Autoloader(array(
    'namespace' => 'DartsGame',
    'basePath'  => APPLICATION_PATH . '/',
));

$loader
    ->addResourceType('controller', 'library/DartsGame/Controller/', 'Controller')
    ->addResourceType('service', 'library/DartsGame/Service/', 'Service')
    ->addResourceType('model', 'models/', 'Model');

// Register Database Adapter for Test
$db = new Zend_Db_Adapter_Pdo_Mysql(array(
    'host' => DB_HOST,
    'username' => DB_USER,
    'password' => DB_PASS,
    'dbname'   => DB_NAME));
Zend_Db_Table::setDefaultAdapter($db);

Zend_Session::start();
