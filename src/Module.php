<?php

namespace Olcs\Logging;

use Laminas\Log\Logger;

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
        ];

        return [
            'listeners' => [
                'Olcs\Logging\Listener\LogRequest',
                'Olcs\Logging\Listener\LogError'
            ],
            'service_manager' => [
                'abstract_factories' => [
                    'Laminas\Log\LoggerAbstractServiceFactory'
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
     * @param \Laminas\EventManager\EventInterface $event Event
     *
     * @return void
     */
    public function onBootstrap(\Laminas\EventManager\EventInterface $event)
    {
        $logger = $event->getApplication()->getServiceManager()->get('Logger');
        $config = $event->getApplication()->getServiceManager()->get('Config');

        if (!isset($config['log']['allowPasswordLogging']) || $config['log']['allowPasswordLogging'] !== true) {
            $logger->addProcessor(Log\Processor\HidePassword::class);
        }

        Logger::registerExceptionHandler($logger);
        Logger::registerErrorHandler($logger);
        Logger::registerFatalErrorShutdownFunction($logger);

        // Set up the static logger
        \Olcs\Logging\Log\Logger::setLogger($logger);
    }
}
