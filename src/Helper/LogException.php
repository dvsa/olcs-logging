<?php

namespace Olcs\Logging\Helper;

use Zend\Log\LoggerAwareTrait;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LogException implements FactoryInterface
{
    use LoggerAwareTrait;

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

        $this->getLogger()->err('', $lastException);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setLogger($serviceLocator->get('ExceptionLogger'));
        return $this;
    }
}
