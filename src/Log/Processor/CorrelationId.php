<?php

namespace Olcs\Logging\Log\Processor;

use Zend\Log\Processor\ProcessorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class CorrelationId
 * @package Olcs\Logging\Log\Processor
 */
class CorrelationId implements ProcessorInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    private $identifier;

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

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getServiceLocator()->getServiceLocator()->get('Request');
        if ($request instanceof \Zend\Http\PhpEnvironment\Request) {
            /** @var \Zend\Http\Header\GenericHeader $correlationHeader */
            $correlationHeader = $request->getHeader('X-Correlation-Id');
            if ($correlationHeader) {
                $this->identifier = $correlationHeader->getFieldValue();
            }
        }

        return $this->identifier;
    }
}
