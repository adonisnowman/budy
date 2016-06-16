<?php 
error_reporting(E_ALL);

try {

	$dir = '/admin/';
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(dirname(__FILE__)).$dir);
    }

    
    define("CONFIGFILE_PATH", ROOT_PATH . '/phalcon/config/config.ini');
   
    require_once ROOT_PATH . '/phalcon/library/Bootstrap.php';    
    require_once ROOT_PATH . '/phalcon/extend/Extendphalcon.php';
    require_once ROOT_PATH . '/phalcon/plugins/Security.php';

    // Instantiate the DI container
    $di  = new \Phalcon\DI\FactoryDefault();

    
    $app = new Bootstrap($di);
  
    echo $app->init(array());

} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
}


?>
