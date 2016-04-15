<?php

namespace Olcs\Logging\Log\Processor;

use Zend\Log\Processor\ProcessorInterface;

/**
 * Class UserId
 * @package Olcs\Logging\Log\Processor
 */
class UserId implements ProcessorInterface
{
    static private $userId;

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        self::$userId = $userId;
    }

    /**
     * @param string $userId
     */
    public function getUserId()
    {
        return self::$userId;
    }

    /**
     * Processes a log message before it is given to the writers
     *
     * @param  array $event
     * @return array
     */
    public function process(array $event)
    {
        $event['extra']['userId'] = self::$userId;
        return $event;
    }
}
