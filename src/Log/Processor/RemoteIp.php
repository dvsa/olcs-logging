<?php

namespace Olcs\Logging\Log\Processor;

use Laminas\Log\Processor\ProcessorInterface;
use Laminas\Http\PhpEnvironment\RemoteAddress;

class RemoteIp implements ProcessorInterface
{
    /**
     * @var RemoteAddress
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
    public function getRemoteAddress(): RemoteAddress
    {
        if (!$this->remoteAddress instanceof RemoteAddress) {
            $this->remoteAddress = new RemoteAddress();
        }

        return $this->remoteAddress;
    }

    public function setRemoteAddress(RemoteAddress $remoteAddress): void
    {
        $this->remoteAddress = $remoteAddress;
    }
}
