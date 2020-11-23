<?php

namespace Olcs\Logging\Log\Processor;

use \Laminas\Log\Processor\ProcessorInterface;

/**
 * Class HidePassword
 * Strip any password from being logged
 *
 * @package Olcs\Logging\Log\Processor
 */
class HidePassword implements ProcessorInterface
{
    private $replaceWith = '*** HIDDEN PASSWORD ***';

    /**
     * Processes log event and removed any password
     *
     * @param  array $event
     * @return array
     */
    public function process(array $event)
    {
        // recurse through the event
        array_walk_recursive(
            $event,
            function (&$value, $key) {
                // if "password" is in the key or value, then mask the value
                if ((stripos($key, 'password') !== false) ||
                    (is_string($value) && stripos($value, 'password') !== false)
                ) {
                    $value = $this->replaceWith;
                }
            }
        );

        return $event;
    }
}
