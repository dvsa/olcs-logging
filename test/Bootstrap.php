<?php

namespace OlcsTest\Logging;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Mockery as m;

error_reporting(-1);
chdir(dirname(__DIR__));

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $config = array();

    public static function init()
    {
        // Setup the autloader
        $loader = static::initAutoloader();

        // Grab the application config
        $config = array(
            'modules' => array(
                'Logging',
            ),
            'module_listener_options' => array(
                'module_paths' => array(
                    __DIR__ . '/../'
                )
            )
        );

        self::$config = $config;
    }

    protected static function initAutoloader()
    {
        return require('vendor/autoload.php');
    }
}

Bootstrap::init();
