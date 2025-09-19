<?php

namespace Olcs\Logging\Helper;

use Psr\Container\ContainerInterface;
use Laminas\Log\LoggerAwareTrait;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LogError implements FactoryInterface
{
    use LoggerAwareTrait;

    #[\Override]
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LogError
    {
        $this->setLogger($container->get('Logger'));
        return $this;
    }
}
