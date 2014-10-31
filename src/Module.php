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
            ['name' => 'RequestId']
        ];

        return [
            'service_manager' => [
                'abstract_factories' => [
                    'Zend\Log\LoggerAbstractServiceFactory'
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
                    'exceptionhandler' => true,
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
        $event->getApplication()->getServiceManager()->get('ExceptionLogger');
    }
}