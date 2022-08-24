<?php

namespace Olcs\Logging\Log\Processor;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * CorrelationIdFactory
 */
class CorrelationIdFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return CorrelationId
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CorrelationId
    {
        return new CorrelationId(
            $container->getServiceLocator()->get('Request')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return CorrelationId
     */
    public function createService(ServiceLocatorInterface $services): CorrelationId
    {
        return $this($services, CorrelationId::class);
    }
}
