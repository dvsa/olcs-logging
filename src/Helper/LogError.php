<?php

namespace Olcs\Logging\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Log\LoggerAwareTrait;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
