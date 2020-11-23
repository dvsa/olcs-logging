<?php

namespace Olcs\Logging\Log\Processor;

use Laminas\Log\Processor\ProcessorInterface;
use Laminas\Http\PhpEnvironment\RemoteAddress;

/**
 * Class RemoteIp
 * @package Olcs\Logging\Log\Processor
 */
class RemoteIp implements ProcessorInterface
{
    /*
     * @var \Laminas\Http\PhpEnvironment\RemoteAddress
     */
    /**
     * @var
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

    /**
     * @return RemoteAddress
     */
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
