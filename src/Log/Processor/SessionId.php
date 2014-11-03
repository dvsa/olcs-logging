<?php

namespace Olcs\Logging\Log\Processor;

use Zend\Log\Processor\ProcessorInterface;
use Zend\Session\Container;
use Zend\Session\ManagerInterface as Manager;

class SessionId implements ProcessorInterface
{
    protected $sessionManager;

    /**
     * @param Manager $sessionManager
     * @return $this;
     */
    public function setSessionManager($sessionManager)
    {
        $this->sessionManager = $sessionManager;
        return $this;
    }

    /**
     * @return Manager
     */
    public function getSessionManager()
    {
        if (is_null($this->sessionManager)) {
            $this->sessionManager = Container::getDefaultManager();
        }
        return $this->sessionManager;
    }

    /**
     * Processes a log message before it is given to the writers
     *
     * @param  array $event
     * @return array
     */
    public function process(array $event)
    {
        //This currently uses the php/zend session id, could be altered to use open AM sessid when an auth solution has
        //been implemented
        $this->getSessionManager()->start();
        $event['extra']['sessionId'] = $this->getSessionManager()->getId();
        return $event;
    }
}
