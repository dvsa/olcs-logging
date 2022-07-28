<?php

namespace Olcs\Logging\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Log\LoggerAwareTrait;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Log\Logger;

/**
 * Class LogError
 * @package Olcs\Logging\Helper
 */
class LogError implements FactoryInterface
{
    use LoggerAwareTrait;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LogError
    {
        $this->setLogger($container->get('Logger'));
        return $this;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     * @deprecated Not needed in Laminas 3
     */
    public function createService(ServiceLocatorInterface $serviceLocator): LogError
    {
        return $this($serviceLocator, LogError::class);
    }
}
