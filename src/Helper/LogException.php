<?php

namespace Olcs\Logging\Helper;

use Interop\Container\ContainerInterface;
use Zend\Log\LoggerAwareTrait;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        return $this($serviceLocator, self::class);
    }

    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setLogger($container->get('Logger'));
        return $this;
    }
}
