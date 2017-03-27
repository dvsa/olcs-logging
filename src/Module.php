<?php

namespace Olcs\Logging;

use Zend\Log\Logger;

/**
 * Class Module
 * @package Olcs\Logging
 */
class Module
{
    /**
     * Get config
     *
     * @return array
     */
    public function getConfig()
    {
        $logfile = sys_get_temp_dir() . '/olcs-' . PHP_SAPI . '-application.log';

        $processors = [
            ['name' => 'Olcs\Logging\Log\Processor\Microtime'],
            ['name' => 'Olcs\Logging\Log\Processor\UserId'],
            ['name' => 'Olcs\Logging\Log\Processor\SessionId'],
            ['name' => 'Olcs\Logging\Log\Processor\RemoteIp'],
            ['name' => Log\Processor\RequestId::class],
            ['name' => Log\Processor\CorrelationId::class],
            ['name' => Log\Processor\HidePassword::class],
        ];

        return [
            'listeners' => [
                'Olcs\Logging\Listener\LogRequest',
                'Olcs\Logging\Listener\LogError'
            ],
            'service_manager' => [
                'abstract_factories' => [
                    'Zend\Log\LoggerAbstractServiceFactory'
                ],
                'factories' => [
                    'Olcs\Logging\Listener\LogRequest' => 'Olcs\Logging\Listener\LogRequest',
                    'Olcs\Logging\Listener\LogError' => 'Olcs\Logging\Listener\LogError',
                    'Olcs\Logging\Helper\LogException' => 'Olcs\Logging\Helper\LogException',
                    'Olcs\Logging\Helper\LogError' => 'Olcs\Logging\Helper\LogError'
                ]
            ],
            'log' => [
                'Logger' => [
                    'processors' => $processors,
                    'filters' => [

                    ],
                    'writers' => [
                        'full' => [
                            'name' => 'stream',
                            'options' => [
                                'stream' => $logfile,
                                'formatter' => 'Olcs\Logging\Log\Formatter\Standard'
                            ],
                        ]
                    ]
                ],
            ]
        ];
    }

    /**
     * onBoostrap
     *
     * @param \Zend\EventManager\EventInterface $event Event
     *
     * @return void
     */
    public function onBootstrap(\Zend\EventManager\EventInterface $event)
    {
        $logger = $event->getApplication()->getServiceManager()->get('Logger');
        Logger::registerExceptionHandler($logger);
        Logger::registerErrorHandler($logger);
        Logger::registerFatalErrorShutdownFunction($logger);

        // Set up the static logger
        \Olcs\Logging\Log\Logger::setLogger($logger);
    }
}
