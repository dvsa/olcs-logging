<?php

namespace Olcs\Logging\Log\Processor;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Log\Processor\ProcessorInterface;
use Laminas\Stdlib\RequestInterface;

/**
 * Class CorrelationId
 * @package Olcs\Logging\Log\Processor
 */
class CorrelationId implements ProcessorInterface
{
    /**
     * @var string
     */
    private $identifier;

    /** @var RequestInterface */
    protected $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
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

        if ($this->request instanceof HttpRequest) {
            /** @var \Laminas\Http\Header\GenericHeader $correlationHeader */
            $correlationHeader = $this->request->getHeader('X-Correlation-Id');

            if ($correlationHeader) {
                $this->identifier = $correlationHeader->getFieldValue();
            }
        }

        return $this->identifier;
    }
}
