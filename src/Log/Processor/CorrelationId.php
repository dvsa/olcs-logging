<?php

namespace Olcs\Logging\Log\Processor;

use Interop\Container\ContainerInterface;
use Zend\Log\Processor\ProcessorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CorrelationId
 * @package Olcs\Logging\Log\Processor
 */
class CorrelationId implements ProcessorInterface, FactoryInterface
{
    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;

    /**
     * @var string
     */
    private $identifier;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return object
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
        $this->request = $container->get('Request');

        return $this;
    }

    /**
     * Process a log event
     *
     * @param array $event Log event to process
     *
     * @return array
     */
    public function process(array $event)
    {
        $event['extra']['correlationId'] = $this->getIdentifier();
        return $event;
    }

    /**
     * Get the correlaction identifier
     *
     * @return string
     */
    protected function getIdentifier()
    {
        if ($this->identifier) {
            return $this->identifier;
        }

        if ($this->request instanceof \Zend\Http\PhpEnvironment\Request) {
            /** @var \Zend\Http\Header\GenericHeader $correlationHeader */
            $correlationHeader = $this->request->getHeader('X-Correlation-Id');
            if ($correlationHeader) {
                $this->identifier = $correlationHeader->getFieldValue();
            }
        }

        return $this->identifier;
    }
}
