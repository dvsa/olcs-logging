<?php

namespace Olcs\Logging\Log\Processor;

use Zend\Log\Processor\ProcessorInterface;

/**
 * Class UserId
 * @package Olcs\Logging\Log\Processor
 */
class UserId implements ProcessorInterface
{
    /**
     * Processes a log message before it is given to the writers
     *
     * @param  array $event
     * @return array
     */
    public function process(array $event)
    {
        $event['extra']['userId'] = '1';
        return $event;
    }
}
