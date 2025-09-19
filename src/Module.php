<?php

namespace Olcs\Logging;

use Laminas\EventManager\EventInterface;
use Laminas\Log\Logger;
use Laminas\Mvc\MvcEvent;
use Olcs\Logging\Log\Formatter\Standard;
use Olcs\Logging\Log\Formatter\StandardFactory;

class Module
{
    public function getConfig(): array
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
                                'formatter' => Standard::class
                            ],
                        ]
                    ]
                ],
            ],
            'log_formatters' => [
                'factories' => [
                    Standard::class => StandardFactory::class,
                ],
            ],
            'log_processors' => [
                'factories' => [
                    Log\Processor\CorrelationId::class => Log\Processor\CorrelationIdFactory::class,
                ],
            ],
        ];
    }

    public function onBootstrap(EventInterface $event): void
    {
        /** @var MvcEvent $event */
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
