<?php

namespace Olcs\Logging\Helper;

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
