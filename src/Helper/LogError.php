<?php

namespace Olcs\Logging\Helper;

use Interop\Container\ContainerInterface;
use Zend\Log\LoggerAwareTrait;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;

/**
 * Class LogError
 * @package Olcs\Logging\Helper
 */
class LogError implements FactoryInterface
{
    use LoggerAwareTrait;

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
