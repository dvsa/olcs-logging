<?php

namespace Olcs\Logging\Helper;

use Laminas\Log\LoggerAwareTrait;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class LogException
 * @package Olcs\Logging\Helper
 */
class LogException implements FactoryInterface
{
    use LoggerAwareTrait;

    /**
     * @param $exception
     * @param array $messageData
     */
    public function logException($exception, $messageData = [])
    {
        $logMessages = [];

        do {
            $messageData['exception'] = $exception;
            $logMessages[] = $messageData;
            $exception = $exception->getPrevious();
        } while ($exception);

        $lastException = array_shift($logMessages);

        foreach (array_reverse($logMessages) as $logMessage) {
            $this->getLogger()->info('', $logMessage);
        }

        $this->getLogger()->err(
            get_class($lastException['exception']) .' : '. $lastException['exception']->getMessage(),
            $lastException
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setLogger($serviceLocator->get('Logger'));
        return $this;
    }
}
