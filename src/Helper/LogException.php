<?php

namespace Olcs\Logging\Helper;

use Psr\Container\ContainerInterface;
use Laminas\Log\LoggerAwareTrait;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
            get_class($lastException['exception']) . ' : ' . $lastException['exception']->getMessage(),
            $lastException
        );
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LogException
    {
        $this->setLogger($container->get('Logger'));
        return $this;
    }
}
