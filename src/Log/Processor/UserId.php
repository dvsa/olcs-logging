<?php

namespace Olcs\Logging\Log\Processor;

use Laminas\Log\Processor\ProcessorInterface;

class UserId implements ProcessorInterface
{
    private static $userId;

    /**
     * @param ?string $userId
     */
    public function setUserId($userId): void
    {
        self::$userId = $userId;
    }

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
