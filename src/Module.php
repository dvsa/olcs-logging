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
            ['name' => 'RequestId']
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
                'ExceptionLogger' => [
                    'processors' => $processors,
                    'writers' => [
                        'full' => [
                            'name' => 'stream',
                            'options' => [
                                'stream' => $logfile,
                                'formatter' => 'Olcs\Logging\Log\Formatter\Exception'
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param \Zend\EventManager\EventInterface $event
     */
    public function onBootstrap(\Zend\EventManager\EventInterface $event)
    {
        $handler = $event->getApplication()->getServiceManager()->get('Olcs\Logging\Helper\LogException');
        Logger::registerExceptionHandler($handler->getLogger());

        $handler = $event->getApplication()->getServiceManager()->get('Olcs\Logging\Helper\LogError');
        Logger::registerErrorHandler($handler->getLogger());
        Logger::registerFatalErrorShutdownFunction($handler->getLogger());
    }
}
