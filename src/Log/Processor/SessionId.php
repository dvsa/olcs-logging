<?php

namespace Olcs\Logging\Log\Processor;

use Laminas\Log\Processor\ProcessorInterface;
use Laminas\Session\Container;
use Laminas\Session\ManagerInterface as Manager;

/**
 * Class SessionId
 * @package Olcs\Logging\Log\Processor
 */
class SessionId implements ProcessorInterface
{
    /** @var Manager */
    protected $sessionManager;

    public function setSessionManager(Manager $sessionManager): SessionId
    {
        $this->sessionManager = $sessionManager;
        return $this;
    }

    public function getSessionManager(): Manager
    {
        if (!$this->sessionManager instanceof Manager) {
            $this->sessionManager = Container::getDefaultManager();
        }
        return $this->sessionManager;
    }

    /**
     * Processes a log message before it is given to the writers
     */
    public function process(array $event): array
    {
        //This currently uses the php/laminas session id, could be altered to use open AM sessid when an auth solution has
        //been implemented
        $this->getSessionManager()->start();
        $event['extra']['sessionId'] = $this->getSessionManager()->getId();
        return $event;
    }
}
