<?php

namespace Olcs\Logging\Log\Processor;

use Zend\Log\Processor\ProcessorInterface;

class Microtime implements ProcessorInterface
{
    /**
     * Processes a log message before it is given to the writers
     *
     * @param  array $event
     * @return array
     */
    public function process(array $event)
    {
        $microtime = explode(' ', microtime());
        $event['microsecs'] = substr($microtime[0], 2, 6);

        return $event;
    }
}
