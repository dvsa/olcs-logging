<?php

namespace Olcs\Logging\Log\Processor;

use Zend\Log\Processor\ProcessorInterface;
use Zend\Http\PhpEnvironment\RemoteAddress;

class RemoteIp implements ProcessorInterface
{
    /*
     * @var \Zend\Http\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * Processes a log message before it is given to the writers
     *
     * @param  array $event
     * @return array
     */
    public function process(array $event)
    {
        $event['extra']['remoteIp'] = $this->getRemoteAddress()->getIpAddress();

        return $event;
    }

    public function getRemoteAddress()
    {
        if (is_null($this->remoteAddress)) {
            $this->remoteAddress = new RemoteAddress();
        }

        return $this->remoteAddress;
    }

    /**
     * @param mixed $remoteAddress
     */
    public function setRemoteAddress($remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;
    }
}
