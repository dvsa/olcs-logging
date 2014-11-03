<?php

namespace Olcs\Logging;

class Module
{
    public function getConfig()
    {
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
                                'stream' => sys_get_temp_dir() . '/logfile.log',
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
                                'stream' => sys_get_temp_dir() . '/logfile.log',
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
        set_exception_handler([$handler, 'logException']);

        $handler = $event->getApplication()->getServiceManager()->get('Olcs\Logging\Helper\LogError');
        set_error_handler([$handler, 'logError']);
        register_shutdown_function([$handler, 'logShutdownError']);
    }
}