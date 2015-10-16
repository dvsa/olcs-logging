<?php

namespace Olcs\Logging\Helper;

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
        $this->setLogger($serviceLocator->get('Logger'));
        return $this;
    }
}
